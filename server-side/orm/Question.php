<?php

require_once('Course.php');
require_once('User.php');
require_once('Professor.php');
/**
 * User: jsullivan
 * Date: 12/3/13
 * Time: 10:19 PM
 * To change this template use File | Settings | File Templates.
 */
class Question
{
    private $id;
    private $text;
    private $course;
    private $user;
    private $semester;
    private $professor;

    public static function create($text, $course, $user, $semester, $professor) {
        $mysqli = new mysqli("classroom.cs.unc.edu", "snydere", "cs4us", "snyderedb");
        $courseid = "NULL";
        $professorid = "NULL";
        if($course){
            $courseid = $course->getID();
        }if($professor){
            $professorid = $professor->getID();
        }
	$resultstr= "insert into Question values (0, " .
            "'" . $mysqli->real_escape_string($text) . "', " .
             $courseid . ", " .
             $user->getID() . ", '" .
            $mysqli->real_escape_string($semester) . "', " .
            $professorid . ")";
	//return $resultstr;
        $result = $mysqli->query("insert into Question values (0, " .
            "'" . $mysqli->real_escape_string($text) . "', " .
            $courseid . ", " .
            $user->getID() . ", '" .
            $mysqli->real_escape_string($semester) . "', " .
            $professorid . ")");

        if ($result) {

            $id = $mysqli->insert_id;
            return new Question($id, $text, $course, $user, $semester, $professor);
        }
        return null;
    }

    public static function findByID($id) {
        $mysqli = new mysqli("classroom.cs.unc.edu", "snydere", "cs4us", "snyderedb");

        $result = $mysqli->query("select * from Question where ID = " . $id);
        if ($result) {
            if ($result->num_rows == 0) {
                return null;
            }

            $question_info = $result->fetch_array();

            $course = Course::findByID(intval($question_info['Course_ID']));
            $user = User::findByID($question_info['User_ID']);
            $professor = Professor::findByID($question_info['Professor_ID']);

            return new Question(intval($question_info['ID']),
                $question_info['Question_Text'],
                $course,
                $user,
                $question_info['Semester'],
                $professor);
        }
        return null;
    }

    public static function getAll() {
        $mysqli = new mysqli("classroom.cs.unc.edu", "snydere", "cs4us", "snyderedb");

        $result = $mysqli->query("select ID from Question");
        $question_array = array();

        if ($result) {
            while ($next_row = $result->fetch_array()) {
                $nextID = intval($next_row['ID']);
                $question_array[] = Question::findByID($nextID)->getJSON();
            }
        }
        return $question_array;
    }

    private function __construct($id, $text, $course, $user, $semester, $professor) {
        $this->id = $id;
        $this->text = $text;
        $this->course = $course;
        $this->user = $user;
        $this->semester = $semester;
        $this->professor = $professor;
    }

    public function getID() {
        return $this->id;
    }

    public function getText() {
        return $this->text;
    }

    public function getCourse() {
        return $this->course;
    }

    public function getUser() {
        return $this->user;
    }

    public function getSemester() {
        return $this->semester;
    }

    public function getProfessor() {
        return $this->professor;
    }

    public function setText($text) {
        $this->text = $text;
        return $this->update();
    }

    public function setCourse($course) {
        $this->course = $course;
        return $this->update();
    }

    public function setUser($user) {
        $this->user = $user;
        return $this->update();
    }

    public function setSemester($semester) {
        $this->semester = $semester;
        return $this->update();
    }

    public function setProfessor($professor) {
        $this->professor = $professor;
        return $this->update();
    }

    private function update() {
        $mysqli = new mysqli("classroom.cs.unc.edu", "snydere", "cs4us", "snyderedb");

        $result = $mysqli->query("update Question set " .
            "Question_Text=" .
            "'" . $mysqli->real_escape_string($this->text) . "', " .
            "Course_ID=" .
            "'" . $mysqli->real_escape_string($this->course->getID()) . "', " .
            "User_ID=" .
            "'" . $mysqli->real_escape_string($this->user->getID()) . "', " .
            "Semester=" . $mysqli->real_escape_string($this->semester) . ", " .
            "Professor_ID='" . $this->professor->getID() . "' " .
            " where ID=" . $this->id);
        return $result;
    }

    public function getJSON() {
        $course = "";
        $professor = "";
        if($this->course){
            $course = $this->course->getJSON();
        }if($this->professor){
            $professor = $this->professor->getJSON();
        }
        $json_obj = array('id' => $this->id,
            'text' => $this->text,
            'course' => $course,
            'user' => $this->user->getJSON(),
            'semester' => $this->semester,
            'professor' => $professor);
        return json_encode($json_obj);
    }
}
