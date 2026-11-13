<?php

session_start();

if(!isset($_SESSION['admin_id']))
{
    header("Location: login.php");
    exit();
}

include("../database/database.php");

$id = intval($_GET['id'] ?? 0);

$result = mysqli_query(
    $conn,
    "SELECT * FROM foods WHERE id='$id'"
);

$food = mysqli_fetch_assoc($result);


if(!$food)
{
    header("Location: foods.php");
    exit();
}


if(isset($_POST['update_food']))
{

$food_name = mysqli_real_escape_string(
    $conn,
    $_POST['food_name']
);

$description = mysqli_real_escape_string(
    $conn,
    $_POST['description']
);

$price = floatval($_POST['price']);

$discount = intval($_POST['discount']);

$image_name = $food['image'];


if(
    isset($_FILES['image']) &&
    $_FILES['image']['error'] == 0
)
{

$extension = pathinfo(
    $_FILES['image']['name'],
    PATHINFO_EXTENSION
);

$image_name =
time() . "_" .
preg_replace(
    "/[^A-Za-z0-9]/",
    "_",
    pathinfo(
        $_FILES['image']['name'],
        PATHINFO_FILENAME
    )
)
.".".$extension;


move_uploaded_file(
    $_FILES['image']['tmp_name'],
    "../uploads/foods/".$image_name
);

}


$query = "UPDATE foods SET

food_name='$food_name',

description='$description',

price='$price',

discount='$discount',

image='$image_name'

WHERE id='$id'
";


if(mysqli_query($conn,$query))
{

header("Location: foods.php");
exit();

}

}

?>

<!DOCTYPE html>

<html>

<head>

<title>Edit Food - MealMate</title>

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

<div class="col-md-7">

<div class="card p-4">

<h2 class="heading">
✏️ Edit Food
</h2>

<form
method="POST"
enctype="multipart/form-data"
>


<div class="mb-3">

<label>
Food Name
</label>

<input
type="text"
name="food_name"
class="form-control"
value="<?php echo htmlspecialchars($food['food_name']); ?>"
required
>

</div>


<div class="mb-3">

<label>
Description
</label>

<textarea
name="description"
class="form-control"
rows="4"
required
><?php echo htmlspecialchars($food['description']); ?></textarea>

</div>


<div class="mb-3">

<label>
Price
</label>

<input
type="number"
name="price"
class="form-control"
value="<?php echo $food['price']; ?>"
required
>

</div>


<div class="mb-3">

<label>
Discount %
</label>

<input
type="number"
name="discount"
class="form-control"
value="<?php echo $food['discount']; ?>"
min="0"
max="100"
>

</div>


<div class="mb-3">

<label>
Change Image
</label>

<input
type="file"
name="image"
class="form-control"
accept="image/*"
>

</div>


<button
type="submit"
name="update_food"
class="btn btn-danger w-100"
>
Update Food
</button>


<a
href="foods.php"
class="btn btn-secondary w-100 mt-2"
>
Cancel
</a>


</form>

</div>

</div>

</div>

</div>

</body>

</html>