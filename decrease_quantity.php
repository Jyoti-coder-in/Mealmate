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

    $query = mysqli_query(
        $conn,
        "SELECT quantity
         FROM cart
         WHERE id='$cart_id'
         AND user_id='$user_id'"
    );

    if(mysqli_num_rows($query) == 1){

        $row = mysqli_fetch_assoc($query);

        if($row['quantity'] > 1){

            mysqli_query(
                $conn,
                "UPDATE cart
                 SET quantity=quantity-1
                 WHERE id='$cart_id'
                 AND user_id='$user_id'"
            );

        }else{

            mysqli_query(
                $conn,
                "DELETE FROM cart
                 WHERE id='$cart_id'
                 AND user_id='$user_id'"
            );
        }
    }
}

header("Location: cart.php");
exit();

?>