<?php

$conn = mysqli_connect(
    "127.0.0.1",
    "root",
    "",
    "mealmate_db",
    3307
);

if (!$conn) {
    die("Database Connection Failed: " . mysqli_connect_error());
}

?>