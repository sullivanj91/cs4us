<?php

require_once('orm/Question.php');
require_once('orm/Comment.php');

$path_components = explode('/', $_SERVER['PATH_INFO']);

// Note that since extra path info starts with '/'
// First element of path_components is always defined and always empty.

if ($_SERVER['REQUEST_METHOD'] == "GET") {
    // GET means either instance look up, index generation, or deletion

    // Following matches instance URL in form
    // /comments.php/<id>

    if ((count($path_components) >= 2) &&
        ($path_components[1] != "")) {

        // Interpret <id> as integer
        $question_id = intval($path_components[1]);

        // Look up object via ORM
        $comment = Question::findByID($question_id);

        if ($comment == null) {
            // Question not found.
            header("HTTP/1.0 404 Not Found");
            print("Comment id: " . $question_id . " not found.");
            exit();
        }

        // Normal lookup.
        // Generate JSON encoding as response
        header("Content-type: application/json");
        print(json_encode(Comment::findByQuestionID($question_id)));
        exit();

    }

    // ID not specified, then must be asking for index
    header("Content-type: application/json");
    print(json_encode(Comment::getAll()));
    exit();

} else if ($_SERVER['REQUEST_METHOD'] == "POST") {

    // Either creating or updating

    // Following matches /comments.php/<id> form
    if ((count($path_components) >= 2) &&
        ($path_components[1] != "")) {

        //Interpret <id> as integer and look up via ORM
        $comment_id = intval($path_components[1]);
        $comment = Comment::findByID($comment_id);

        if ($comment == null) {
            // Question not found.
            header("HTTP/1.0 404 Not Found");
            print("Comment id: " . $comment_id . " not found while attempting update.");
            exit();
        }

        // Validate values
        $new_question_id = false;
        if (isset($_REQUEST['question_id'])) {
            $new_question_id = intval($_REQUEST['question_id']);
            if ($new_question_id == "") {
                header("HTTP/1.0 400 Bad Request");
                print("Bad title");
                exit();
            }
        }

        $new_user_id = false;
        if (isset($_REQUEST['user_id'])) {
            $new_user_id = intval($_REQUEST['user_id']);
        }

        $new_comment_text = false;
        if (isset($_REQUEST['comment_text'])) {
            $new_comment_text = trim($_REQUEST['comment_text']);
        }

        // Update via ORM
        if ($new_question_id) {
            $comment->setQuestionID($new_question_id);
        }
        if ($new_user_id != false) {
            $comment->setUserID($new_user_id);
        }
        if ($new_comment_text != false) {
            $comment->setCommentText($new_comment_text);
        }

        // Return JSON encoding of updated Question
        header("Content-type: application/json");
        print($comment->getJSON());
        exit();
    } else {

        // Creating a new Question item

        // Validate values
        if (!isset($_REQUEST['question_id'])) {
            header("HTTP/1.0 400 Bad Request");
            print("Missing comment question_id");
            exit();
        }

        $question_id = intval($_REQUEST['question_id']);
        if ($question_id == "") {
            header("HTTP/1.0 400 Bad Request");
            print("Need comment question_id");
            exit();
        }

        $user_id = "";
        if (isset($_REQUEST['user_id'])) {
            $user_id = intval($_REQUEST['user_id']);
        }

        $comment_text = "";
        if (isset($_REQUEST['comment_text'])) {
            $comment_text = trim($_REQUEST['comment_text']);
        }

	$user = User::findByID($user_id);

        // Create new Question via ORM
        $new_comment = Comment::create($question_id, $user, $comment_text);

        // Report if failed
        if ($new_comment == null) {
            header("HTTP/1.0 500 Server Error");
            print("Server couldn't create new Comment.");
            exit();
        }

        //Generate JSON encoding of new Question
        header("Content-type: application/json");
        print($new_comment->getJSON());
        exit();
    }
}

// If here, none of the above applied and URL could
// not be interpreted with respect to RESTful conventions.

header("HTTP/1.0 400 Bad Request");
print("Did not understand URL");
