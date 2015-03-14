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
class Interview_Question
{
    private $id;
    private $text;
    private $company;
    private $user;
    private $year;

    public static function create($text, $company, $year, $user) {
        $mysqli = new mysqli("classroom.cs.unc.edu", "snydere", "cs4us", "snyderedb");
        
        /*$resultstr =("insert into Interview_Questions values (0, " .
            "'" . $user->getID() . "', " .
            "'" . $mysqli->real_escape_string($text) . "',' " .
            $mysqli->real_escape_string($company) . "', " .
            $year . ")");*/
        $result = $mysqli->query("insert into Interview_Questions values (0, " .
            "'" . $user->getID() . "', " .
            "'" . $mysqli->real_escape_string($text) . "', '" .
            $mysqli->real_escape_string($company) . "',' " .
            $year . "')");
	//return $resultstr;
        if  ($result) {

            $id = $mysqli->insert_id;
            return new Interview_Question($id, $text, $company, $year, $user);
        }
        return null;
    }

    public static function findByID($id) {
        $mysqli = new mysqli("classroom.cs.unc.edu", "snydere", "cs4us", "snyderedb");

        $result = $mysqli->query("select * from Interview_Questions where ID = " . $id);
        if ($result) {
            if ($result->num_rows == 0) {
                return null;
            }

            $question_info = $result->fetch_array();

            $user = User::findByID($question_info['User_ID'])->getJSON();

            return new Interview_Question(intval($question_info['ID']),
                $question_info['Question_Text'],
                $question_info['Company'],
                $question_info['Year'],
                $user);
        }
        return null;
    }

    public static function getAll() {
        $mysqli = new mysqli("classroom.cs.unc.edu", "snydere", "cs4us", "snyderedb");

        $result = $mysqli->query("select ID from Interview_Questions");
        $question_array = array();

        if ($result) {
            while ($next_row = $result->fetch_array()) {
                $nextID = intval($next_row['ID']);
                $question_array[] = Interview_Question::findByID($nextID)->getJSON();
            }
        }
        return $question_array;
    }

    private function __construct($id, $text, $company, $year, $user) {
        $this->id = $id;
        $this->text = $text;
        $this->company = $company;
        $this->user = $user;
        $this->year = $year;
    }

    public function getID() {
        return $this->id;
    }

    public function getText() {
        return $this->text;
    }

    public function getCompany() {
        return $this->company;
    }

    public function getUser() {
        return $this->user;
    }

    public function getYear() {
        return $this->year;
    }

    public function setText($text) {
        $this->text = $text;
        return $this->update();
    }

    public function setCompany($company) {
        $this->company = $company;
        return $this->update();
    }

    public function setUser($user) {
        $this->user = $user;
        return $this->update();
    }

    public function setYear($year) {
        $this->year = $year;
        return $this->update();
    }


    private function update() {
        $mysqli = new mysqli("classroom.cs.unc.edu", "snydere", "cs4us", "snyderedb");

        $result = $mysqli->query("update Interview_Questions set " .
            "Question_Text=" .
            "'" . $mysqli->real_escape_string($this->text) . "', " .
            "Company=" .
            "'" . $mysqli->real_escape_string($this->company) . "', " .
            "User_ID=" .
            "'" . $mysqli->real_escape_string($this->user->getID()) . "', " .
            "Year=" . $mysqli->real_escape_string($this->year) . ", " .
            " where ID=" . $this->id);
        return $result;
    }

    public function getJSON() {

        $json_obj = array('id' => $this->id,
            'text' => $this->text,
            'company' => $this->company,
            'user' => $this->user,
            'year' => $this->year);
        return json_encode($json_obj);
    }
}
