<?php
session_start();
header('Content-type: application/json');
print(json_encode($_SESSION['userid']));