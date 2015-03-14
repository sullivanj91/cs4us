<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jsullivan
 * Date: 12/3/13
 * Time: 11:33 PM
 * To change this template use File | Settings | File Templates.
 */
class Professor
{
    private $id;
    private $name;
    private $bio;

    public static function findByID($id) {
        $mysqli = new mysqli("classroom.cs.unc.edu", "snydere", "cs4us", "snyderedb");

        $result = $mysqli->query("select * from Professor where ID = " . $id);
        if ($result) {
            if ($result->num_rows == 0) {
                return null;
            }

            $professor_info = $result->fetch_array();

            //note here that the database will probably return just the course_id and user_ID
            //so you will have to go get the user and course objects before you return a new question
            //also you will probably have to change key values to db column names
            return new Professor(intval($professor_info['ID']),
                $professor_info['Name'],
                $professor_info['Bio']);
        }
        return null;
    }

    public static function getAll() {
        $mysqli = new mysqli("classroom.cs.unc.edu", "snydere", "cs4us", "snyderedb");

        $result = $mysqli->query("select ID from Professor");
        $professor_array = array();

        if ($result) {
            while ($next_row = $result->fetch_array()) {
                $nextID = intval($next_row['ID']);
                $professor_array[] = Professor::findByID($nextID)->getJSON();
            }
        }
        return $professor_array;
    }

    private function __construct($id, $name, $bio) {
        $this->id = $id;
        $this->name = $name;
        $this->bio = $bio;
    }

    public function getID() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getBio() {
        return $this->bio;
    }

    public function setName($name) {
        $this->name = $name;
        return $this->update();
    }

    public function setBio($bio) {
        $this->bio = $bio;
        return $this->update();
    }

    private function update() {
        $mysqli = new mysqli("classroom.cs.unc.edu", "snydere", "cs4us", "snyderedb");

        $result = $mysqli->query("update Professor set " .
            "Bio=" .
            "'" . $mysqli->real_escape_string($this->bio) . "', " .
            "Name=" .
            "'" . $mysqli->real_escape_string($this->name) . "', " .
            " where id=" . $this->id);
        return $result;
    }

    public function getJSON() {

        $json_obj = array('id' => $this->id,
            'name' => $this->name,
            'bio' => $this->bio);
        return json_encode($json_obj);
    }
}
