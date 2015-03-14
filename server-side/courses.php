<?php
session_start();
require_once('orm/Course.php');

$path_components = explode('/', $_SERVER['PATH_INFO']);
require_once('authenticate.php');

// Note that since extra path info starts with '/'
// First element of path_components is always defined and always empty.

if ($_SERVER['REQUEST_METHOD'] == "GET") {
    // GET means either instance look up, index generation, or deletion

    // Following matches instance URL in form
    // /course.php/<id>

    if ((count($path_components) >= 2) &&
        ($path_components[1] != "")) {

        // Interpret <id> as integer
        $course_id = intval($path_components[1]);

        // Look up object via ORM
        $course = Course::findByID($course_id);

        if ($course == null) {
            // Question not found.
            header("HTTP/1.0 404 Not Found");
            print("Course id: " . $course_id . " not found.");
            exit();
        }

        // Normal lookup.
        // Generate JSON encoding as response
        header("Content-type: application/json");
        print($course->getJSON());
        exit();

    }

    // ID not specified, then must be asking for index
    header("Content-type: application/json");
    print(json_encode(Course::getAll()));
    exit();
}

// If here, none of the above applied and URL could
// not be interpreted with respect to RESTful conventions.

header("HTTP/1.0 400 Bad Request");
print("Did not understand URL");

?>