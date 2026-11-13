<?php

session_start();

if(!isset($_SESSION['student_id'])){
    header("Location: ../login.php");
    exit();
}

include("../database/database.php");

$user_id = $_SESSION['student_id'];

if(isset($_GET['id'])){

    $cart_id = intval($_GET['id']);

    mysqli_query(
        $conn,
        "UPDATE cart
         SET quantity=quantity+1
         WHERE id='$cart_id'
         AND user_id='$user_id'"
    );
}

header("Location: cart.php");
exit();

?>