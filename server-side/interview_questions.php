<?php
session_start();
require_once('authenticate.php');

require_once('orm/Interview_Question.php');
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
        $question = Interview_Question::findByID($question_id);

        if ($question == null) {
            // Interview_Question not found.
            header("HTTP/1.0 404 Not Found");
            print("Interview_Question id: " . $question_id . " not found.");
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
   print(json_encode(Interview_Question::getAll()));
    exit();

} else if ($_SERVER['REQUEST_METHOD'] == "POST") {

    // Either creating or updating

    // Following matches /questions.php/<id> form
    if ((count($path_components) >= 2) &&
        ($path_components[1] != "")) {

        //Interpret <id> as integer and look up via ORM
        $question_id = intval($path_components[1]);
        $question = Interview_Question::findByID($question_id);

        if ($question == null) {
            // Interview_Question not found.
            header("HTTP/1.0 404 Not Found");
            print("Interview_Question id: " . $question_id . " not found while attempting update.");
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

        $new_company = false;
        if (isset($_REQUEST['company'])) {
            $new_company = trim($_REQUEST['company']);
        }

        $new_user = false;
        if (isset($_REQUEST['user'])) {
            $new_user = trim($_REQUEST['user']);
        }

        $new_year = false;
        if (isset($_REQUEST['year'])) {
            $new_year = trim($_REQUEST['year']);
        }

	$user = User::findByID($new_user);
        // Update via ORM
        if ($new_text) {
            $question->setText($new_text);
        }
        if ($new_company != false) {
            $question->setCompany($new_company);
        }
        if ($new_user != false) {
            $question->setUser($user);
        }
        if ($new_year) {
            $question->setYear($new_year);
        }

        // Return JSON encoding of updated Interview_Question
        header("Content-type: application/json");
        print($question->getJSON());
        exit();
    } else {

        // Creating a new Interview_Question item

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

        $company = "";
        if (isset($_REQUEST['company'])) {
            $company = trim($_REQUEST['company']);
        }

        $userID = "";
        if (isset($_REQUEST['user'])) {
            $userID = intval($_REQUEST['user']);
        }

        $year = "";
        if (isset($_REQUEST['year'])) {
            $year = trim($_REQUEST['year']);
        }


        $user = User::findByID($userID);


        // Create new Interview_Question via ORM
        $new_question = Interview_Question::create($text, $company, $year, $user);
	// Report if failed
	//print_r($new_question);
        if ($new_question == null) {
            header("HTTP/1.0 500 Server Error");
            print("Server couldn't create new Interview_Question.");
            exit();
        }

        //Generate JSON encoding of new Interview_Question
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
