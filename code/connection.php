<?php
/*
// Connect to the MySQL database
$conn = new mysqli("localhost:3306", "root", "Brycehall22", "eoc_environmental");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
*/
?>


<?php

$host = 'eoc-environmental.cpwukqa44fwy.us-east-2.rds.amazonaws.com';
$db = 'eoc_environmental';
$user = 'root';
$password = 'RootPass2000';

// Create connection
$conn = new mysqli($host, $user, $password, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>
