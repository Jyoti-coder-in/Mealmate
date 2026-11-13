<?php

session_start();

if(!isset($_SESSION['admin_id']))
{
    header("Location: login.php");
    exit();
}

include("../database/database.php");

$id = intval($_GET['id'] ?? 0);

if($id > 0)
{
    mysqli_query(
        $conn,
        "DELETE FROM foods WHERE id='$id'"
    );
}

header("Location: foods.php");
exit();

?>