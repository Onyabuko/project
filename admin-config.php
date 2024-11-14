<?php
//config file to handle database connections

$host = 'localhost';
$db = 'SGS';
$user = 'root'; // Change this to your database username
$pass = ''; // Change this to your database password

$mysqli = new mysqli($host, $user, $pass, $db);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}
?>
