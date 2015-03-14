<?php
session_start();
//todo do we need to include authenticate.php?
require_once('orm/User.php');

$path_components = explode('/', $_SERVER['PATH_INFO']);

// Note that since extra path info starts with '/'
// First element of path_components is always defined and always empty.

if ($_SERVER['REQUEST_METHOD'] == "GET") {
    // GET means either instance look up, index generation, or deletion

    // Following matches instance URL in form
    // /users.php/<id>

    if ((count($path_components) >= 2) &&
        ($path_components[1] != "")) {

        // Interpret <id> as integer
        $user_id = intval($path_components[1]);

        // Look up object via ORM
        $user = User::findByID($user_id);

        if ($user == null) {
            // Question not found.
            header("HTTP/1.0 404 Not Found");
            print("User id: " . $user_id . " not found.");
            exit();
        }

        // Normal lookup.
        // Generate JSON encoding as response
        header("Content-type: application/json");
        print($user->getJSON());
        exit();

    }
    //todo don't think we need this
    // ID not specified, then must be asking for index
    header("Content-type: application/json");
    print(json_encode(User::getAllUsers()));
    exit();

} else if ($_SERVER['REQUEST_METHOD'] == "POST") {
    //todo may just need to put require(authenticate.php)
    //todo inside update code
    // Either creating or updating

    // Following matches /users.php/<id> form
    if ((count($path_components) >= 2) &&
        ($path_components[1] != "")) {

        //Interpret <id> as integer and look up via ORM
        $user_id = intval($path_components[1]);
        $user = User::findByID($user_id);

        if ($user == null) {
            // Question not found.
            header("HTTP/1.0 404 Not Found");
            print("User id: " . $user_id . " not found while attempting update.");
            exit();
        }

        // Validate values
        $new_username = false;
        if (isset($_REQUEST['username'])) {
            $new_username = trim($_REQUEST['username']);
            if ($new_username == "") {
                header("HTTP/1.0 400 Bad Request");
                print("Bad title");
                exit();
            }
        }

        $new_password = false;
        if (isset($_REQUEST['password'])) {
            $new_password = trim($_REQUEST['password']);
                if ($new_password == "") {
                    header("HTTP/1.0 400 Bad Request");
                    print("Bad title");
                    exit();
                }
        }

        $new_display_name = false;
        if (isset($_REQUEST['display_name'])) {
            $new_display_name = trim($_REQUEST['display_name']);
        }

        $new_email = false;
        if (isset($_REQUEST['email'])) {
            $new_email = trim($_REQUEST['email']);
        }


        // Update via ORM
        if ($new_username) {
            $user->setUsername($new_username);
        }
        if ($new_password != false) {
            $user->setPassword($new_password);
        }
        if ($new_display_name != false) {
            $user->setDisplay_Name($new_display_name);
        }
        if ($new_email) {
            $user->setEmail($new_email);
        }

        // Return JSON encoding of updated Question
        header("Content-type: application/json");
        print($user->getJSON());
        exit();
    } else {

        // Creating a new Question item

        // Validate values
        if (!isset($_REQUEST['username'])) {
            header("HTTP/1.0 400 Bad Request");
            print("Missing username");
            exit();
        }

        $username = trim($_REQUEST['username']);
        if ($username == "") {
            header("HTTP/1.0 400 Bad Request");
            print("Need username");
            exit();
        }

        $password = "";
        if (isset($_REQUEST['password'])) {
            $password = trim($_REQUEST['password']);
                if ($password == "") {
                    header("HTTP/1.0 400 Bad Request");
                    print("Need password");
                    exit();
                }
        }

        $display_name = "";
        if (isset($_REQUEST['display_name'])) {
            $display_name = trim($_REQUEST['display_name']);
        }

        $email = "";
        if (isset($_REQUEST['email'])) {
            $email = trim($_REQUEST['email']);
        }


        // Create new Question via ORM
        $new_user = User::create($username, $password, $display_name, $email);

        // Report if failed
        if ($new_user == null) {
            header("HTTP/1.0 500 Server Error");
            print("Server couldn't create new User.");
            exit();
        }

        //Generate JSON encoding of new Question
        header("Content-type: application/json");
        print($new_user->getJSON());
        exit();
    }
}

// If here, none of the above applied and URL could
// not be interpreted with respect to RESTful conventions.

header("HTTP/1.0 400 Bad Request");
print("Did not understand URL");

?>