<?php

$host = "localhost";
$user = "root";
$password = "";
$database = "mealmate_db";
$port = 3307;

$conn = mysqli_connect(
    $host,
    $user,
    $password,
    $database,
    $port
);

if (!$conn) {
    die("Database Connection Failed: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");

?>