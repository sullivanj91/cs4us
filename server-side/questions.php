<?php
session_start();
require_once('authenticate.php');

require_once('orm/Question.php');
require_once('orm/Course.php');
require_once('orm/User.php');
require_once('orm/Professor.php');
$path_components = explode('/', $_SERVER['PATH_INFO']);

// Note that since extra path info starts with '/'
// First element of path_components is always defined and always empty.

if ($_SERVER['REQUEST_METHOD'] == "GET") {
    // GET means either instance look up, index generation, or deletion

    // Following matches instance URL in form
    // /questions.php/<id>

    if ((count($path_components) >= 2) &&
        ($path_components[1] != "")) {

        // Interpret <id> as integer
        $question_id = intval($path_components[1]);

        // Look up object via ORM
        $question = Question::findByID($question_id);

        if ($question == null) {
            // Question not found.
            header("HTTP/1.0 404 Not Found");
            print("Question id: " . $question_id . " not found.");
            exit();
        }

        // Normal lookup.
        // Generate JSON encoding as response
        header("Content-type: application/json");
        print($question->getJSON());
        exit();

    }

    // ID not specified, then must be asking for index
    header("Content-type: application/json");
    print(json_encode(Question::getAll()));
    exit();

} else if ($_SERVER['REQUEST_METHOD'] == "POST") {

    // Either creating or updating

    // Following matches /questions.php/<id> form
    if ((count($path_components) >= 2) &&
        ($path_components[1] != "")) {

        //Interpret <id> as integer and look up via ORM
        $question_id = intval($path_components[1]);
        $question = Question::findByID($question_id);

        if ($question == null) {
            // Question not found.
            header("HTTP/1.0 404 Not Found");
            print("Question id: " . $question_id . " not found while attempting update.");
            exit();
        }

        // Validate values
        $new_text = false;
        if (isset($_REQUEST['text'])) {
            $new_text = trim($_REQUEST['text']);
            if ($new_text == "") {
                header("HTTP/1.0 400 Bad Request");
                print("Bad title");
                exit();
            }
        }

        $new_course = false;
        if (isset($_REQUEST['course'])) {
            $new_course = intval($_REQUEST['course']);
        }

        $new_user = false;
        if (isset($_REQUEST['user'])) {
            $new_user = intval($_REQUEST['user']);
        }

        $new_semester = false;
        if (isset($_REQUEST['semester'])) {
            $new_semester = trim($_REQUEST['semester']);
        }

        $new_professor = false;
        if (isset($_REQUEST['professor'])) {
            $new_professor = intval($_REQUEST['professor']);
        }

        $course = Course::findByID($new_course);
	print_r($course);
        $user = User::findByID($new_user);
        $professor = Professor::findByID($new_professor);
        // Update via ORM
        if ($new_user != false) {
            $question->setUser($question->getUser());
        }
        if ($new_course != false) {
	    print_r($course);
            $question->setCourse($question->getCourse());
        }
        if ($new_professor != false) {
            $question->setProfessor($question->getProfessor());
        }
        if ($new_text) {
            $question->setText($new_text);
        }
        if ($new_semester) {
            $question->setSemester($new_semester);
        }

        // Return JSON encoding of updated Question
        header("Content-type: application/json");
        print($question->getJSON());
        exit();
    } else {

        // Creating a new Question item

        // Validate values
        if (!isset($_REQUEST['text'])) {
            header("HTTP/1.0 400 Bad Request");
            print("Missing question text");
            exit();
        }

        $text = trim($_REQUEST['text']);
        if ($text == "") {
            header("HTTP/1.0 400 Bad Request");
            print("Need question text");
            exit();
        }

        $courseID = "";
        if (isset($_REQUEST['course'])) {
            $courseID = intval($_REQUEST['course']);
        }

        $userID = "";
        if (isset($_REQUEST['user'])) {
            $userID = intval($_REQUEST['user']);
        }

        $semester = "";
        if (isset($_REQUEST['semester'])) {
            $semester = trim($_REQUEST['semester']);
        }

        $professorID = "";
        if (isset($_REQUEST['professor'])) {
            $professorID = intval($_REQUEST['professor']);
        }

        $course = Course::findByID($courseID);
        $user = User::findByID($userID);
        $professor = Professor::findByID($professorID);


        // Create new Question via ORM
        $new_question = Question::create($text, $course, $user, $semester, $professor);
	print_r($new_question);
        // Report if failed
        if ($new_question == null) {
            header("HTTP/1.0 500 Server Error");
            print("Server couldn't create new Question.");
            exit();
        }

        //Generate JSON encoding of new Question
        header("Content-type: application/json");
        print($new_question->getJSON());
        exit();
    }
}

// If here, none of the above applied and URL could
// not be interpreted with respect to RESTful conventions.

header("HTTP/1.0 400 Bad Request");
print("Did not understand URL");

?>
