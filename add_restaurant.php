<?php

session_start();

if(!isset($_SESSION['admin_id']))
{
    header("Location: login.php");
    exit();
}

include("../database/database.php");

$message = "";

if(isset($_POST['add_restaurant']))
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

$image_name = "";


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


$upload_folder = "../uploads/";

if(!is_dir($upload_folder))
{
    mkdir(
        $upload_folder,
        0777,
        true
    );
}


move_uploaded_file(
    $_FILES['image']['tmp_name'],
    $upload_folder.$image_name
);

}

}


$sql = "INSERT INTO restaurants
(
restaurant_name,
owner_name,
phone,
email,
address,
description,
image,
opening_time,
closing_time,
status
)
VALUES
(
'$restaurant_name',
'$owner_name',
'$phone',
'$email',
'$address',
'$description',
'$image_name',
'$opening_time',
'$closing_time',
'$status'
)";


if(mysqli_query($conn,$sql))
{

header("Location: restaurants.php");
exit();

}
else
{

$message =
"Error: " .
mysqli_error($conn);

}

}

?>

<!DOCTYPE html>

<html>

<head>

<title>Add Restaurant - MealMate</title>

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

<div class="container py-5">

<div class="row justify-content-center">

<div class="col-lg-8">

<div class="card p-4">

<h2 class="heading mb-4">
🍽️ Add Restaurant
</h2>


<?php

if($message != "")
{

?>

<div class="alert alert-danger">
<?php echo $message; ?>
</div>

<?php

}

?>


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
placeholder="Enter restaurant name"
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
placeholder="Enter owner name"
required
>

</div>


<div class="col-md-6 mb-3">

<label class="fw-bold">
Phone Number
</label>

<input
type="text"
name="phone"
class="form-control"
placeholder="Enter phone number"
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
placeholder="Enter email"
>

</div>


<div class="col-12 mb-3">

<label class="fw-bold">
Restaurant Address
</label>

<textarea
name="address"
class="form-control"
rows="3"
placeholder="Enter complete address"
required
></textarea>

</div>


<div class="col-12 mb-3">

<label class="fw-bold">
Restaurant Description
</label>

<textarea
name="description"
class="form-control"
rows="3"
placeholder="Short description about restaurant"
></textarea>

</div>


<div class="col-md-6 mb-3">

<label class="fw-bold">
Opening Time
</label>

<input
type="time"
name="opening_time"
class="form-control"
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
>

</div>


<div class="col-md-6 mb-3">

<label class="fw-bold">
Restaurant Image
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

<option value="Active">
Active
</option>

<option value="Inactive">
Inactive
</option>

</select>

</div>


</div>


<button
type="submit"
name="add_restaurant"
class="btn btn-red w-100 mt-3"
>
➕ Add Restaurant
</button>


<a
href="restaurants.php"
class="btn btn-secondary w-100 mt-2"
>
Back to Restaurants
</a>


</form>

</div>

</div>

</div>

</div>

</body>

</html>