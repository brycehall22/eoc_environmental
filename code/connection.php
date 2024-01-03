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