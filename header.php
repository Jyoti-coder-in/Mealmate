<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>MealMate</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
    background:#fff0f6;
    font-family:Arial, sans-serif;
}

.navbar{
    background:#4a1834 !important;
}

.navbar-brand{
    color:white !important;
    font-size:25px;
    font-weight:bold;
}

.navbar-brand span{
    color:#d63384;
}

.nav-link{
    color:white !important;
    font-weight:bold;
    margin-left:10px;
}

.nav-link:hover{
    color:#f062a6 !important;
}

.logout-btn{
    background:#d63384;
    border-radius:8px;
    padding:7px 15px !important;
}

</style>

</head>

<body>

<nav class="navbar navbar-expand-lg">

<div class="container">

<a class="navbar-brand" href="../index.php">
🍔 Meal<span>Mate</span>
</a>

<button
class="navbar-toggler bg-light"
type="button"
data-bs-toggle="collapse"
data-bs-target="#navbarMenu"
>
<span class="navbar-toggler-icon"></span>
</button>

<div class="collapse navbar-collapse" id="navbarMenu">

<ul class="navbar-nav ms-auto">

<li class="nav-item">
<a class="nav-link" href="dashboard.php">
🏠 Home
</a>
</li>

<li class="nav-item">
<a class="nav-link" href="foods.php">
🍔 Food Menu
</a>
</li>

<li class="nav-item">
<a class="nav-link" href="cart.php">
🛒 Cart
</a>
</li>

<li class="nav-item">
<a class="nav-link" href="checkout.php">
💳 Checkout
</a>
</li>

<li class="nav-item">
<a class="nav-link" href="orders.php">
📦 My Orders
</a>
</li>

<li class="nav-item">
<a class="nav-link logout-btn" href="../logout.php">
Logout
</a>
</li>

</ul>

</div>

</div>

</nav>

<div class="container py-4">