<?php
include_once __DIR__ . "/../includes/helpers.php";
session_start();

if(!isset($_SESSION['student_id']))
{
    header("Location: ../login.php");
    exit();
}

include("../database/database.php");

$user_id = $_SESSION['student_id'];

$query = mysqli_query(
    $conn,
    "SELECT cart.*,
            foods.food_name,
            foods.price,
            foods.image
     FROM cart
     JOIN foods ON cart.food_id = foods.id
     WHERE cart.user_id='$user_id'
     ORDER BY cart.id DESC"
);

$grand = 0;

?>

<!DOCTYPE html>
<html>

<head>

<title>My Cart - MealMate</title>

<meta name="viewport" content="width=device-width, initial-scale=1">

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet"
>

<style>

body{
    background:#fff0f6;
    color:#4a1834;
}

.navbar{
    background:#4a1834;
}

.navbar-brand,
.nav-link{
    color:white !important;
}

.cart-card{
    background:white;
    border:none;
    border-radius:15px;
    box-shadow:0 4px 15px rgba(0,0,0,.1);
}

.food-img{
    width:100px;
    height:80px;
    object-fit:cover;
    border-radius:10px;
}

.summary{
    background:white;
    border-radius:15px;
    box-shadow:0 4px 15px rgba(0,0,0,.1);
}

.btn-red{
    background:#d63384;
    color:white;
    border:none;
    font-weight:bold;
}

</style>

</head>

<body>


<nav class="navbar">

<div class="container">

<a href="../index.php" class="navbar-brand">
<?php echo mm_logo("../"); ?>
</a>

<div>

<a href="foods.php" class="btn btn-warning me-2">
Food Menu
</a>

<a href="../logout.php" class="btn btn-danger">
Logout
</a>

</div>

</div>

</nav>


<div class="container py-5">

<h2 style="color:#d63384;" class="mb-4">
🛒 Your Cart
</h2>


<div class="row">


<div class="col-lg-8">


<?php

if(mysqli_num_rows($query) == 0)
{

?>

<div class="alert alert-info">

Your cart is empty.

<br><br>

<a href="foods.php" class="btn btn-red">
Browse Food
</a>

</div>

<?php

}


while($row = mysqli_fetch_assoc($query))
{

$total = $row['price'] * $row['quantity'];

$grand += $total;

$image_path = food_image_url($row, "../");

?>


<div class="cart-card p-3 mb-3">

<div class="row align-items-center">


<div class="col-md-2">



<img
src="<?php echo $image_path; ?>"
class="food-img"
>



</div>


<div class="col-md-4">

<h5 style="color:#d63384;">
<?php echo htmlspecialchars($row['food_name']); ?>
</h5>

<p class="mb-0">
₹<?php echo $row['price']; ?>
</p>

</div>


<div class="col-md-3">

<div class="d-flex align-items-center gap-2">

<a
href="decrease_qty.php?id=<?php echo $row['id']; ?>"
class="btn btn-danger btn-sm"
>
−
</a>

<strong>
<?php echo $row['quantity']; ?>
</strong>

<a
href="increase_qty.php?id=<?php echo $row['id']; ?>"
class="btn btn-success btn-sm"
>
+
</a>

</div>

</div>


<div class="col-md-3 text-end">

<strong>
₹<?php echo $total; ?>
</strong>

<br>

<a
href="remove_cart.php?id=<?php echo $row['id']; ?>"
class="btn btn-outline-danger btn-sm mt-2"
>
Remove
</a>

</div>


</div>

</div>


<?php } ?>


</div>


<div class="col-lg-4">

<div class="summary p-4">

<h4>
🧾 Order Summary
</h4>

<hr>

<div class="d-flex justify-content-between">

<span>Subtotal</span>

<strong>
₹<?php echo $grand; ?>
</strong>

</div>


<hr>


<h5 class="d-flex justify-content-between">

<span>Total</span>

<span style="color:#d63384;">
₹<?php echo $grand; ?>
</span>

</h5>


<?php if($grand > 0){ ?>

<a
href="checkout.php"
class="btn btn-red w-100 mt-3"
>
Proceed To Checkout →
</a>

<?php } ?>


</div>

</div>


</div>

</div>

</body>
</html>