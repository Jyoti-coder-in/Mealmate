<?php

session_start();

if(!isset($_SESSION['admin_id']))
{
    header("Location: login.php");
    exit();
}

include("../database/database.php");

$message = "";

if(isset($_POST['add_food']))
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


$image_name = "";


if(isset($_FILES['image']) &&
   $_FILES['image']['error'] == 0)
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


$upload_folder = "../uploads/foods/";

if(!is_dir($upload_folder))
{
    mkdir($upload_folder,0777,true);
}


move_uploaded_file(
    $_FILES['image']['tmp_name'],
    $upload_folder.$image_name
);

}


$sql = "INSERT INTO foods
(food_name, description, price, discount, image)
VALUES
(
'$food_name',
'$description',
'$price',
'$discount',
'$image_name'
)";


if(mysqli_query($conn,$sql))
{

header("Location: foods.php");
exit();

}
else
{

$message =
"Error: " . mysqli_error($conn);

}

}

?>

<!DOCTYPE html>
<html>

<head>

<title>Add Food - MealMate</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet"
>

<style>

body{
    background:#fff0f6;
    color:#4a1834;
}

.card{
    border:none;
    border-radius:20px;
    box-shadow:0 5px 20px rgba(0,0,0,.12);
}

.heading{
    color:#d63384;
}

.btn-red{
    background:#d63384;
    color:white;
    border:none;
}

</style>

</head>

<body>

<div class="container py-5">

<div class="row justify-content-center">

<div class="col-md-7">

<div class="card p-4">

<h2 class="heading mb-4">
➕ Add New Food
</h2>


<?php if($message!=""){ ?>

<div class="alert alert-danger">
<?php echo $message; ?>
</div>

<?php } ?>


<form
method="POST"
enctype="multipart/form-data"
>


<div class="mb-3">

<label class="fw-bold">
Food Name
</label>

<input
type="text"
name="food_name"
class="form-control"
required
>

</div>


<div class="mb-3">

<label class="fw-bold">
Description
</label>

<textarea
name="description"
class="form-control"
rows="4"
required
></textarea>

</div>


<div class="mb-3">

<label class="fw-bold">
Price
</label>

<input
type="number"
name="price"
class="form-control"
min="1"
required
>

</div>


<div class="mb-3">

<label class="fw-bold">
Discount %
</label>

<input
type="number"
name="discount"
class="form-control"
min="0"
max="100"
value="0"
>

</div>


<div class="mb-4">

<label class="fw-bold">
Food Image
</label>

<input
type="file"
name="image"
class="form-control"
accept="image/*"
required
>

</div>


<button
type="submit"
name="add_food"
class="btn btn-red w-100"
>
Add Food
</button>


<a
href="foods.php"
class="btn btn-secondary w-100 mt-2"
>
Back to Foods
</a>


</form>

</div>

</div>

</div>

</div>

</body>

</html>