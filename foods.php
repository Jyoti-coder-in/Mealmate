<?php
include_once __DIR__ . "/../includes/helpers.php";
session_start();

if(!isset($_SESSION['student_id']))
{
    header("Location: ../login.php");
    exit();
}

include("../database/database.php");

$search = trim($_GET['search'] ?? '');
if ($search !== '') {
    $stmt = mysqli_prepare($conn, "SELECT * FROM foods WHERE food_name LIKE ? OR description LIKE ? OR category LIKE ? ORDER BY id DESC");
    $term = '%' . $search . '%';
    mysqli_stmt_bind_param($stmt, 'sss', $term, $term, $term);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($result && mysqli_num_rows($result) === 0) {
        // Smart recommendation fallback: return available alternatives for any unmatched search.
        $result = mysqli_query($conn, "SELECT * FROM foods ORDER BY RAND() LIMIT 6");
        $smartFallback = true;
    } else {
        $smartFallback = false;
    }
} else {
    $smartFallback = false;
    $result = mysqli_query($conn, "SELECT * FROM foods ORDER BY id DESC");
}

?>

<!DOCTYPE html>
<html>

<head>

<title>MealMate Food Menu</title>

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

.navbar-brand{
    font-size:25px;
    font-weight:bold;
}

.food-card{
    background:white;
    border:none;
    border-radius:18px;
    overflow:hidden;
    box-shadow:0 5px 18px rgba(0,0,0,.12);
    height:100%;
    transition:.3s;
}

.food-card:hover{
    transform:translateY(-5px);
}

.food-card img{
    width:100%;
    height:230px;
    object-fit:cover;
}

.food-card h4{
    color:#d63384;
}

.btn-red{
    background:#d63384;
    color:white;
    border:none;
    border-radius:10px;
    font-weight:bold;
    padding:10px;
}

.btn-red:hover{
    background:#b0256b;
    color:white;
}

</style>

</head>

<body>
<?php if ($search !== '' && !empty($smartFallback)): ?>
<div class="container mt-3"><div class="alert alert-info rounded-4">🤖 Smart Recommendation: no exact match for <strong><?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?></strong>, so here are other available foods you can order.</div></div>
<?php endif; ?>

<div class="container mt-3"><form method="get" class="p-3 bg-white rounded-4" style="border:1px solid #f9a8d4"><label class="form-label fw-bold" style="color:#9d174d">Search food menu</label><div class="input-group"><input class="form-control" type="search" name="search" value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Pizza, burger, pasta, dosa..."><button class="btn btn-red" type="submit">Search</button></div></form></div>


<nav class="navbar">

<div class="container">

<a href="../index.php" class="navbar-brand">
<?php echo mm_logo("../"); ?>
</a>

<div class="navbar-nav ms-auto d-flex flex-row">

<a href="dashboard.php" class="nav-link px-3">
Dashboard
</a>

<a href="foods.php" class="nav-link px-3">
Food Menu
</a>

<a href="cart.php" class="nav-link px-3">
🛒 Cart
</a>

<a href="orders.php" class="nav-link px-3">
My Orders
</a>

<a href="../logout.php" class="nav-link px-3">
Logout
</a>

</div>

</div>

</nav>


<div class="container py-5">




<div class="d-flex justify-content-between align-items-center mb-5">

<h2 style="color:#d63384;">
MealMate Food Menu
</h2>

<a href="cart.php" class="btn btn-dark">
🛒 View Cart
</a>

</div>


<div class="row g-4">


<?php

if(mysqli_num_rows($result) > 0)
{

while($food = mysqli_fetch_assoc($result))
{

?>


<div class="col-md-6 col-lg-4">

<div class="food-card">


<?php

$image_path = food_image_url($food, "../");

?>

<img
src="<?php echo htmlspecialchars($image_path); ?>"
alt="<?php echo htmlspecialchars($food['food_name']); ?>"
>


<div class="p-4">

<h4>
<?php echo htmlspecialchars($food['food_name']); ?>
</h4>


<p class="text-muted">
<?php echo htmlspecialchars($food['description']); ?>
</p>


<h5 class="text-success">
₹<?php echo $food['price']; ?>
</h5>


<?php

if($food['discount'] > 0)
{

?>

<span class="badge bg-danger">
<?php echo $food['discount']; ?>% OFF
</span>

<?php

}

?>


<a
href="add_to_cart.php?id=<?php echo $food['id']; ?>"
class="btn btn-red w-100 mt-3"
>
🛒 Add To Cart
</a>

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

<div class="alert alert-warning text-center">

No food items available.

</div>

</div>

<?php

}

?>

</div>

</div>

</body>
</html>