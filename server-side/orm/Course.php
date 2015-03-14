<?php
/**
 * User: jsullivan
 * Date: 12/3/13
 * Time: 10:19 PM
 * To change this template use File | Settings | File Templates.
 */
class Course
{
    private $id;
    private $name;
    private $description;

    public static function findByID($id) {
        $mysqli = new mysqli("classroom.cs.unc.edu", "snydere", "cs4us", "snyderedb");

        $result = $mysqli->query("select * from Course where ID = " . $id);
        if ($result) {
            if ($result->num_rows == 0) {
                return null;
            }

            $course_info = $result->fetch_array();
            //note here that the database will probably return just the course_id and user_ID
            //so you will have to go get the user and course objects before you return a new question
            //also you will probably have to change key values to db column names
            return new Course(intval($course_info['ID']),
                $course_info['Description'],
                $course_info['Name']);
        }
        return null;
    }

    public static function getAll() {
        $mysqli = new mysqli("classroom.cs.unc.edu", "snydere", "cs4us", "snyderedb");

        $result = $mysqli->query("select ID from Course");
        $course_array = array();

        if ($result) {
            while ($next_row = $result->fetch_array()) {
                $nextID = intval($next_row['ID']);
                $course_array[] = Course::findByID($nextID)->getJSON();
            }
        }
        return $course_array;
    }

    private function __construct($id, $name, $description) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
    }

    public function getID() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getDescription() {
        return $this->description;
    }

    public function setName($name) {
        $this->name = $name;
        return $this->update();
    }

    public function setDescription($description) {
        $this->description = $description;
        return $this->update();
    }

    private function update() {
        $mysqli = new mysqli("classroom.cs.unc.edu", "snydere", "cs4us", "snyderedb");

        $result = $mysqli->query("update Course set " .
            "Description=" .
            "'" . $mysqli->real_escape_string($this->description) . "', " .
            "Name=" .
            "'" . $mysqli->real_escape_string($this->name) . "', " .
            " where id=" . $this->id);
        return $result;
    }

    public function getJSON() {

        $json_obj = array('id' => $this->id,
            'name' => $this->name,
            'description' => $this->description);
        return json_encode($json_obj);
    }
}