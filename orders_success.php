<?php

session_start();

if(!isset($_SESSION['student_id'])){
    header("Location: ../login.php");
    exit();
}

$order_id = isset($_GET['id'])
    ? intval($_GET['id'])
    : 0;

?>

<!DOCTYPE html>
<html>

<head>

<title>MealMate - Order Successful</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
    background:#fff0f6;
}

.success-box{
    max-width:600px;
    margin:100px auto;
    background:white;
    padding:50px;
    border-radius:20px;
    box-shadow:0 5px 20px rgba(0,0,0,.15);
}

</style>

</head>

<body>

<div class="container">

<div class="success-box text-center">

<div style="font-size:70px;">
✅
</div>

<h1 style="color:#d63384;">
Order Placed Successfully!
</h1>

<p class="lead">
Thank you for ordering with MealMate.
</p>
<?php if (($_GET["payment"] ?? "") === "UPI") { ?><div class="alert alert-warning">UPI order received. If you have not paid yet, pay <strong>8744851505@ybl</strong> using your UPI app and keep your transaction reference. Payment is not auto-verified on this demo site.</div><?php } ?>

<p>

Your Order ID:

<strong>
#<?php echo $order_id; ?>
</strong>

</p>


<a
href="orders.php"
class="btn btn-dark"
>
View My Orders
</a>

<a
href="foods.php"
class="btn btn-danger"
>
Order More Food
</a>

</div>

</div>

</body>
</html>