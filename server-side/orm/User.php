<?php
/**
 * User: jsullivan
 * Date: 12/3/13
 * Time: 11:40 PM
 * To change this template use File | Settings | File Templates.
 */
class User
{
    private $id;
    private $username;
    private $password;
    private $salt;
    private $display_name;
    private $email;

    public static function create($username, $password, $display_name, $email) {
        $mysqli = new mysqli("classroom.cs.unc.edu", "snydere", "cs4us", "snyderedb");
        $salt = uniqid(mt_rand(), true);

        $result = $mysqli->query("insert into User values (0, " .
            "'" . $mysqli->real_escape_string($username) . "', " .
            "'" . $mysqli->real_escape_string($password) . "', " .
            "'" . $salt . "', " .
            "'" . $mysqli->real_escape_string($display_name) . "', '" .
            $mysqli->real_escape_string($email) . "')");

        if ($result) {
            $id = $mysqli->insert_id;
            return new User($id, $username, $password, $salt, $display_name, $email);
        }
        return null;
    }

    public static function findByID($id) {
        $mysqli = new mysqli("classroom.cs.unc.edu", "snydere", "cs4us", "snyderedb");

        $result = $mysqli->query("select * from User where ID = " . $id);
        if ($result) {
            if ($result->num_rows == 0) {
                return null;
            }

            $user_info = $result->fetch_array();
            //note here that the database will probably return just the course_id and user_ID
            //so you will have to go get the user and course objects before you return a new question
            //also you will probably have to change key values to db column names
            return new User(intval($user_info['ID']),
                $user_info['Username'],
                $user_info['Password'],
                $user_info['Salt'],
                $user_info['Display_Name'],
                $user_info['Email']);
        }
        return null;
    }

    public static function getAllUsers() {
        $mysqli = new mysqli("classroom.cs.unc.edu", "snydere", "cs4us", "snyderedb");

        $result = $mysqli->query("select * from User");
        $user_array = array();

        if ($result) {
            while ($next_row = $result->fetch_array()) {
                $nextUser = new User(intval($next_row['ID']),
                    $next_row['Username'],
                    $next_row['Password'],
                    $next_row['Salt'],
                    $next_row['Display_Name'],
                    $next_row['Email']);
                $user_array[] = $nextUser;
            }
        }
        return $user_array;
    }

    private function __construct($id, $username, $password, $salt, $display_name, $email) {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->salt = $salt;
        $this->display_name = $display_name;
        $this->email = $email;
    }

    public function getID() {
        return $this->id;
    }

    public function getUsername() {
        return $this->username;
    }

    public function getPassword() {
        return $this->password;
    }

    public function getSalt() {
        return $this->salt;
    }

    public function getDisplay_Name() {
        return $this->display_name;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setUsername($username) {
        $this->username = $username;
        return $this->update();
    }

    public function setPassword($password) {
        $this->password = $password;
        return $this->update();
    }

    public function setDisplay_Name($display_name) {
        $this->display_name = $display_name;
        return $this->update();
    }

    public function setEmail($email) {
        $this->email = $email;
        return $this->update();
    }

    private function update() {
        $mysqli = new mysqli("classroom.cs.unc.edu", "snydere", "cs4us", "snyderedb");
        $result = $mysqli->query("update User set " .
            "Username=" .
            "'" . $mysqli->real_escape_string($this->username) . "', " .
            "Password=" .
            "'" . $mysqli->real_escape_string($this->password) . "', " .
            "Display_Name=" .
            "'" . $mysqli->real_escape_string($this->display_name) . "', " .
            "Email='" . $mysqli->real_escape_string($this->email) . "' " .
            " where id=" . $this->id);
        print_r($mysqli->affected_rows);
        return $result;
    }

    public function getJSON() {

        $json_obj = array('id' => $this->id,
            'username' => $this->username,
            'display_name' => $this->display_name,
            'email' => $this->email);
        return json_encode($json_obj);
    }
}
