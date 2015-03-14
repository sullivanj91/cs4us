<?php

require_once('orm/User.php');
session_start();

function check_password($username, $password) {

  $user_array = User::getAllUsers();

  foreach($user_array as $user) {

        $uname = $user->getUsername();
        $usalt = $user->getSalt();
        $upassword = $user->getPassword();
        $uhash = md5($usalt . $upassword);

        if ($uname == $username) {
          if (md5($usalt . $password) == $uhash){
              $_SESSION['userid'] = $user->getID();
              print_r($_SESSION['userid']);
            return true;
          }
        }
  }
  return false;
}

$username = $_GET['username'];
$password = $_GET['password'];

if (check_password($username, $password)) {

  // Generate authorization cookie
  $_SESSION['username'] = $username;
  $_SESSION['authsalt'] = time();

  $auth_cookie_val = md5($_SESSION['username'] . $_SERVER['REMOTE_ADDR'] . $_SESSION['authsalt']);
  setcookie('CS4USAUTH', $auth_cookie_val, 0, '/Courses/comp426-f13/snydere/cs4us', 'wwwp.cs.unc.edu', false);


} else {
  unset($_SESSION['username']);
  unset($_SESSION['authsalt']);

  header('HTTP/1.1 401 Unauthorized');
  header('Content-type: application/json');
  print(json_encode(false));
}