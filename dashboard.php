
<?php
<?php

$conn = mysqli_connect(
    "localhost",
    "root",
    "",
    "mealmate_db",
    3306
);

if (!$conn) {
    die("Database Connection Failed: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");

?>

<!DOCTYPE html>
<html>
<head>
    <title>MealMate Vendor Dashboard</title>
    <style>
        body {
            font-family: Arial;
            background: #fff5f8;
            margin: 0;
        }
        .sidebar {
            width: 230px;
            height: 100vh;
            background: #29232a;
            position: fixed;
            padding: 20px;
            box-sizing: border-box;
        }
        .sidebar h2 { color: #ff91bd; }
        .sidebar a {
            display: block;
            color: white;
            padding: 13px;
            text-decoration: none;
            margin: 8px 0;
            border-radius: 6px;
        }
        .sidebar a:hover { background: #d63384; }
        .main { margin-left: 230px; padding: 35px; }
        .card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            max-width: 600px;
            box-shadow: 0 3px 12px #0001;
        }
        .card a { color: #d63384; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>MealMate Vendor</h2>
    <p style="color:white">
        <?= h($_SESSION['vendor_name'] ?? 'Vendor') ?>
    </p>

    <a href="dashboard.php">Dashboard</a>
    <a href="manage_food.php">Manage Food Items</a>
    <a href="orders.php">Manage Orders</a>
    <a href="logout.php">Logout</a>
</div>

<div class="main">
    <div class="card">
        <h1>Welcome to Vendor Dashboard!</h1>
        <p>Your vendor account is logged in successfully.</p>
        <h3>Vendor Management</h3>
        <p><a href="manage_food.php">Manage Food Items →</a></p>
        <p><a href="orders.php">Manage Orders →</a></p>
    </div>
</div>

</body>
</html>
