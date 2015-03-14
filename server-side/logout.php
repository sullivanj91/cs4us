<?php

require_once('orm/User.php');
session_start();

  unset($_SESSION['username']);
  unset($_SESSION['authsalt']);

  //header('HTTP/1.1 401 Unauthorized');
  //header('Content-type: application/json');
  //print(json_encode(false));

