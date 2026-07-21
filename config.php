
<?php
// Database configuration

$host = "127.0.0.1";
$user = "root";
$password = "jaffa@0000";
$database = "users_db";

$conn = mysqli_connect($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

?>