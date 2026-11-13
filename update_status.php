<?php

session_start();

if(!isset($_SESSION['admin_id']))
{
    header("Location: login.php");
    exit();
}

include("../database/database.php");

$id = intval($_GET['id'] ?? 0);


if(isset($_POST['update']))
{

$status = mysqli_real_escape_string(
    $conn,
    $_POST['order_status']
);

mysqli_query(
    $conn,
    "UPDATE orders
     SET order_status='$status'
     WHERE id='$id'"
);

header("Location: orders.php");
exit();

}


$result = mysqli_query(
    $conn,
    "SELECT * FROM orders
     WHERE id='$id'"
);

$order = mysqli_fetch_assoc($result);


if(!$order)
{
    header("Location: orders.php");
    exit();
}

?>

<!DOCTYPE html>

<html>

<head>

<title>Update Order Status</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet"
>

<style>

body{
    background:#fff0f6;
}

.card{
    background:white;
    border:none;
    border-radius:20px;
    box-shadow:0 5px 20px rgba(0,0,0,.12);
}

.heading{
    color:#d63384;
}

</style>

</head>

<body>

<div class="container py-5">

<div class="row justify-content-center">

<div class="col-md-6">

<div class="card p-4">

<h2 class="heading">
📦 Update Order
</h2>

<p>
Order ID:
<strong>
#<?php echo $order['id']; ?>
</strong>
</p>


<form method="POST">

<label class="fw-bold">
Order Status
</label>

<select
name="order_status"
class="form-select mt-2 mb-4"
required
>

<option value="Pending">
Pending
</option>

<option value="Confirmed">
Confirmed
</option>

<option value="Preparing">
Preparing
</option>

<option value="Out for Delivery">
Out for Delivery
</option>

<option value="Delivered">
Delivered
</option>

<option value="Cancelled">
Cancelled
</option>

</select>


<button
type="submit"
name="update"
class="btn btn-danger w-100"
>
Update Status
</button>


<a
href="orders.php"
class="btn btn-secondary w-100 mt-2"
>
Back to Orders
</a>


</form>

</div>

</div>

</div>

</div>

</body>

</html>