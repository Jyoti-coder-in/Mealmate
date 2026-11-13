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
    "SELECT * FROM restaurants WHERE id='$id'"
);

$restaurant = mysqli_fetch_assoc($result);


if(!$restaurant)
{
    header("Location: restaurants.php");
    exit();
}


if(isset($_POST['update_restaurant']))
{

$restaurant_name = mysqli_real_escape_string(
    $conn,
    $_POST['restaurant_name']
);

$owner_name = mysqli_real_escape_string(
    $conn,
    $_POST['owner_name']
);

$phone = mysqli_real_escape_string(
    $conn,
    $_POST['phone']
);

$email = mysqli_real_escape_string(
    $conn,
    $_POST['email']
);

$address = mysqli_real_escape_string(
    $conn,
    $_POST['address']
);

$description = mysqli_real_escape_string(
    $conn,
    $_POST['description']
);

$opening_time = $_POST['opening_time'];

$closing_time = $_POST['closing_time'];

$status = $_POST['status'];

$image_name = $restaurant['image'];


if(
    isset($_FILES['image']) &&
    $_FILES['image']['error'] == 0
)
{

$extension = strtolower(
    pathinfo(
        $_FILES['image']['name'],
        PATHINFO_EXTENSION
    )
);

$allowed = [
    'jpg',
    'jpeg',
    'png',
    'webp'
];


if(in_array($extension,$allowed))
{

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
    "../uploads/".$image_name
);

}

}


$query = "UPDATE restaurants SET

restaurant_name='$restaurant_name',

owner_name='$owner_name',

phone='$phone',

email='$email',

address='$address',

description='$description',

image='$image_name',

opening_time='$opening_time',

closing_time='$closing_time',

status='$status'

WHERE id='$id'
";


if(mysqli_query($conn,$query))
{

header("Location: restaurants.php");
exit();

}

}

?>

<!DOCTYPE html>

<html>

<head>

<title>Edit Restaurant - MealMate</title>

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
    background:white;
    border:none;
    border-radius:20px;
    box-shadow:0 5px 20px rgba(0,0,0,.12);
}

.heading{
    color:#d63384;
    font-weight:bold;
}

</style>

</head>

<body>

<div class="container py-5">

<div class="row justify-content-center">

<div class="col-lg-8">

<div class="card p-4">

<h2 class="heading mb-4">
✏️ Edit Restaurant
</h2>


<form
method="POST"
enctype="multipart/form-data"
>


<div class="row">


<div class="col-md-6 mb-3">

<label class="fw-bold">
Restaurant Name
</label>

<input
type="text"
name="restaurant_name"
class="form-control"
value="<?php echo htmlspecialchars($restaurant['restaurant_name']); ?>"
required
>

</div>


<div class="col-md-6 mb-3">

<label class="fw-bold">
Owner Name
</label>

<input
type="text"
name="owner_name"
class="form-control"
value="<?php echo htmlspecialchars($restaurant['owner_name']); ?>"
required
>

</div>


<div class="col-md-6 mb-3">

<label class="fw-bold">
Phone
</label>

<input
type="text"
name="phone"
class="form-control"
value="<?php echo htmlspecialchars($restaurant['phone']); ?>"
required
>

</div>


<div class="col-md-6 mb-3">

<label class="fw-bold">
Email
</label>

<input
type="email"
name="email"
class="form-control"
value="<?php echo htmlspecialchars($restaurant['email']); ?>"
>

</div>


<div class="col-12 mb-3">

<label class="fw-bold">
Address
</label>

<textarea
name="address"
class="form-control"
rows="3"
required
><?php echo htmlspecialchars($restaurant['address']); ?></textarea>

</div>


<div class="col-12 mb-3">

<label class="fw-bold">
Description
</label>

<textarea
name="description"
class="form-control"
rows="3"
><?php echo htmlspecialchars($restaurant['description']); ?></textarea>

</div>


<div class="col-md-6 mb-3">

<label class="fw-bold">
Opening Time
</label>

<input
type="time"
name="opening_time"
class="form-control"
value="<?php echo $restaurant['opening_time']; ?>"
>

</div>


<div class="col-md-6 mb-3">

<label class="fw-bold">
Closing Time
</label>

<input
type="time"
name="closing_time"
class="form-control"
value="<?php echo $restaurant['closing_time']; ?>"
>

</div>


<div class="col-md-6 mb-3">

<label class="fw-bold">
Change Image
</label>

<input
type="file"
name="image"
class="form-control"
accept="image/*"
>

</div>


<div class="col-md-6 mb-3">

<label class="fw-bold">
Status
</label>

<select
name="status"
class="form-select"
>

<option
value="Active"
<?php
if($restaurant['status']=='Active')
echo 'selected';
?>
>
Active
</option>

<option
value="Inactive"
<?php
if($restaurant['status']=='Inactive')
echo 'selected';
?>
>
Inactive
</option>

</select>

</div>


</div>


<button
type="submit"
name="update_restaurant"
class="btn btn-danger w-100"
>
Update Restaurant
</button>


<a
href="restaurants.php"
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