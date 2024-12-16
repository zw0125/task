<?php
$servername = "localhost";
$username = "root";
$password = ""; // Leave blank for XAMPP default
$database = "my_project";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
