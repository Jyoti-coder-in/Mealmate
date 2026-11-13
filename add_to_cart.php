<?php

session_start();

include("../database/database.php");

if(!isset($_SESSION['student_id']))
{
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['student_id'];

if(!isset($_GET['id']))
{
    header("Location: foods.php");
    exit();
}

$food_id = intval($_GET['id']);


/* Check food exists */

$food_query = mysqli_query(
    $conn,
    "SELECT * FROM foods WHERE id='$food_id'"
);

if(mysqli_num_rows($food_query) == 0)
{
    header("Location: foods.php");
    exit();
}


/* Check whether item already exists in cart */

$check = mysqli_query(
    $conn,
    "SELECT * FROM cart
     WHERE user_id='$user_id'
     AND food_id='$food_id'"
);


if(mysqli_num_rows($check) > 0)
{
    mysqli_query(
        $conn,
        "UPDATE cart
         SET quantity = quantity + 1
         WHERE user_id='$user_id'
         AND food_id='$food_id'"
    );
}
else
{
    mysqli_query(
        $conn,
        "INSERT INTO cart (user_id, food_id, quantity)
         VALUES ('$user_id','$food_id',1)"
    );
}


/* Directly open cart */

header("Location: cart.php");
exit();

?>