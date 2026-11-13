<?php
include_once __DIR__ . "/../includes/helpers.php";
session_start();

if(!isset($_SESSION['admin_id']))
{
    header("Location: login.php");
    exit();
}

include("../database/database.php");

$result = mysqli_query(
    $conn,
    "SELECT * FROM restaurants ORDER BY id DESC"
);

?>

<!DOCTYPE html>
<html>

<head>

<title>Restaurants - MealMate Admin</title>

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

.navbar-brand{
    color:white !important;
    font-weight:bold;
    font-size:23px;
}

.heading{
    color:#d63384;
    font-weight:bold;
}

.restaurant-card{
    background:white;
    border:none;
    border-radius:18px;
    overflow:hidden;
    box-shadow:0 5px 20px rgba(0,0,0,.12);
    height:100%;
}

.restaurant-image{
    width:100%;
    height:210px;
    object-fit:cover;
}

.no-image{
    height:210px;
    background:#4a1834;
    color:white;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:70px;
}

.btn-red{
    background:#d63384;
    color:white;
    border:none;
}

.btn-red:hover{
    background:#b0256b;
    color:white;
}

</style>

</head>

<body>

<nav class="navbar p-3">

<div class="container">

<a
href="dashboard.php"
class="navbar-brand"
>
<?php echo mm_logo("../"); ?>
</a>

<div>

<span class="badge bg-warning text-dark me-2">Admin</span>

<a
href="dashboard.php"
class="btn btn-warning me-1"
>
Dashboard
</a>

<a
href="foods.php"
class="btn btn-light me-1"
>
Foods
</a>

<a
href="orders.php"
class="btn btn-light me-1"
>
Orders
</a>

<a
href="logout.php"
class="btn btn-danger"
>
Logout
</a>

</div>

</div>

</nav>


<div class="container py-5">


<div class="d-flex justify-content-between align-items-center mb-4">

<div>

<h2 class="heading">
🍽️ Restaurant Management
</h2>

<p class="mb-0">
Manage restaurants providing food through MealMate.
</p>

</div>

<a
href="add_restaurant.php"
class="btn btn-red"
>
➕ Add Restaurant
</a>

</div>


<div class="row g-4">


<?php

if(mysqli_num_rows($result) > 0)
{

while($restaurant = mysqli_fetch_assoc($result))
{

?>


<div class="col-md-4">

<div class="restaurant-card">


<?php

if(!empty($restaurant['image']))
{

?>

<img
src="../uploads/<?php echo htmlspecialchars($restaurant['image']); ?>"
class="restaurant-image"
alt="Restaurant"
>

<?php

}
else
{

?>

<div class="no-image">
🍽️
</div>

<?php

}

?>


<div class="p-4">

<h4 class="heading">

<?php

echo htmlspecialchars(
$restaurant['restaurant_name']
);

?>

</h4>


<p class="text-muted">

<?php

echo htmlspecialchars(
$restaurant['description'] ?? ''
);

?>

</p>


<p class="mb-1">

<strong>Owner:</strong>

<?php

echo htmlspecialchars(
$restaurant['owner_name']
);

?>

</p>


<p class="mb-1">

<strong>📞 Phone:</strong>

<?php

echo htmlspecialchars(
$restaurant['phone']
);

?>

</p>


<p class="mb-1">

<strong>📍 Address:</strong>

<?php

echo htmlspecialchars(
$restaurant['address']
);

?>

</p>


<?php

if($restaurant['status'] == 'Active')
{

?>

<span class="badge bg-success">
Active
</span>

<?php

}
else
{

?>

<span class="badge bg-danger">
Inactive
</span>

<?php

}

?>


<div class="mt-3">

<a
href="edit_restaurant.php?id=<?php echo $restaurant['id']; ?>"
class="btn btn-warning"
>
✏️ Edit
</a>


<a
href="delete_restaurant.php?id=<?php echo $restaurant['id']; ?>"
class="btn btn-danger"
onclick="return confirm('Are you sure you want to delete this restaurant?');"
>
🗑️ Delete
</a>

</div>


</div>

</div>

</div>


<?php

}

}
else
{

?>

<div class="col-12">

<div class="alert alert-info">

No restaurants added yet.

<a
href="add_restaurant.php"
>
Add your first restaurant
</a>

</div>

</div>

<?php

}

?>


</div>

</div>

</body>

</html>