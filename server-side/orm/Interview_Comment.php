<?php

require_once('User.php');
/**
 * User: jsullivan
 * Date: 12/3/13
 * Time: 10:19 PM
 * To change this template use File | Settings | File Templates.
 */
class Interview_Comment
{
    private $id;
    private $question_id;
    private $user;
    private $comment_text;

    public static function create($question_id, $user, $comment_text) {
        $mysqli = new mysqli("classroom.cs.unc.edu", "snydere", "cs4us", "snyderedb");

        $result = $mysqli->query("insert into Interview_Comments values (0, " .
            "'" . $user->getID() . "', " .
            "'" . $question_id . "', " .
            "'" . $mysqli->real_escape_string($comment_text) . "')");

        if ($result) {

            $id = $mysqli->insert_id;
            return new Interview_Comment($id, $question_id, $user, $comment_text);
        }
        return null;
    }

    public static function findByID($id) {
        $mysqli = new mysqli("classroom.cs.unc.edu", "snydere", "cs4us", "snyderedb");

        $result = $mysqli->query("select * from Interview_Comments where ID = " . $id);
        if ($result) {
            if ($result->num_rows == 0) {
                return null;
            }

            $comment_info = $result->fetch_array();
	    $user = User::findByID($comment_info['User_ID'])->getJSON();
            //note here that the database will probably return just the comment_id and user_ID
            //so you will have to go get the user and comment objects before you return a new question
            //also you will probably have to change key values to db column names
            return new Interview_Comment(intval($comment_info['ID']),
                $comment_info['Interview_Question_ID'],
                $user,
		$comment_info['Comment_Text']);
        }
        return null;
    }
/*
    public static function findByQuestionID($question_id) {
        $mysqli = new mysqli("classroom.cs.unc.edu", "snydere", "cs4us", "snyderedb");

        $result = $mysqli->query("select * from Interview_Comments where Interview_Question_ID = " . $question_id);
        if ($result) {
            if ($result->num_rows == 0) {
                return null;
            }

            $comment_info = $result->fetch_array();
	    $user = User::findByID($comment_info['User_ID'])->getJSON();
            //note here that the database will probably return just the comment_id and user_ID
            //so you will have to go get the user and comment objects before you return a new question
            //also you will probably have to change key values to db column names
            return new Interview_Comment(intval($comment_info['ID']),
                $comment_info['Interview_Question_ID'],
                $user,
		$comment_info['Comment_Text']);
        }
        return null;
    }
*/
    public static function findByQuestionID($question_id) {
        $mysqli = new mysqli("classroom.cs.unc.edu", "snydere", "cs4us", "snyderedb");

        $result = $mysqli->query("select ID from Interview_Comments where Interview_Question_id = " . $question_id);
        $comment_array = array();

        if ($result) {
            while ($next_row = $result->fetch_array()) {
                $nextID = intval($next_row['ID']);
                $comment_array[] = Interview_Comment::findByID($nextID)->getJSON();
            }
        }
        return $comment_array;
    }
    public static function getAll() {
        $mysqli = new mysqli("classroom.cs.unc.edu", "snydere", "cs4us", "snyderedb");

        $result = $mysqli->query("select ID from Interview_Comments");
        $comment_array = array();

        if ($result) {
            while ($next_row = $result->fetch_array()) {
                $nextID = intval($next_row['ID']);
                $comment_array[] = Interview_Comment::findByID($nextID)->getJSON();
            }
        }
        return $comment_array;
    }

    private function __construct($id, $question_id, $user, $comment_text) {
        $this->id = $id;
        $this->question_id = $question_id;
        $this->user = $user;
        $this->comment_text = $comment_text;
    }

    public function getID() {
        return $this->id;
    }

    public function getQuestionID() {
        return $this->question_id;
    }

    public function getUser() {
        return $this->user;
    }

    public function getCommentText() {
        return $this->comment_text;
    }

    public function setQuestionID($question_id) {
        $this->question_id = $question_id;
        return $this->update();
    }

    public function setUser($user) {
        $this->user = $user;
        return $this->update();
    }

    public function setCommentText($comment_text) {
        $this->comment_text = $comment_text;
        return $this->update();
    }

    private function update() {
        $mysqli = new mysqli("classroom.cs.unc.edu", "snydere", "cs4us", "snyderedb");

        $result = $mysqli->query("update Interview_Comments set " .
            "Interview_Question_ID=" .
            "'" . $this->question_id . "', " .
            "User_ID=" .
            "'" . $this->user->getID() . "', " .
            "Comment_Text=" .
            "'" . $mysqli->real_escape_string($this->comment_text) . "', " .
            " where id=" . $this->id);
        return $result;
    }

    public function getJSON() {

        $json_obj = array('id' => $this->id,
            'question_id' => $this->question_id,
            'user' => $this->user,
            'comment_text' => $this->comment_text);
        return json_encode($json_obj);
    }
}
