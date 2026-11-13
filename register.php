<?php
include_once __DIR__ . "/includes/helpers.php";
session_start();

include("database/database.php");

$message = "";

if(isset($_POST['register']))
{

    $name = mysqli_real_escape_string(
        $conn,
        $_POST['name']
    );

    $email = mysqli_real_escape_string(
        $conn,
        $_POST['email']
    );

    $mobile = mysqli_real_escape_string(
        $conn,
        $_POST['mobile']
    );

    $college = mysqli_real_escape_string(
        $conn,
        $_POST['college']
    );

    $password = password_hash(
        $_POST['password'],
        PASSWORD_DEFAULT
    );


    /* CHECK EMAIL */

    $check = mysqli_query(
        $conn,
        "SELECT id FROM users WHERE email='$email'"
    );


    if(mysqli_num_rows($check) > 0)
    {

        $message = "
        <div class='alert alert-danger'>
        Email already registered!
        </div>
        ";

    }

    else
    {

        $sql = "
        INSERT INTO users
        (name,email,mobile,college,password)
        VALUES
        (
        '$name',
        '$email',
        '$mobile',
        '$college',
        '$password'
        )
        ";


        if(mysqli_query($conn,$sql))
        {

            $message = "
            <div class='alert alert-success'>
            Registration Successful!
            <br>
            You can now login.
            </div>
            ";

        }

        else
        {

            $message = "
            <div class='alert alert-danger'>
            Registration Failed:
            ".mysqli_error($conn)."
            </div>
            ";

        }

    }

}

?>

<!DOCTYPE html>

<html>

<head>

<title>MealMate Registration</title>

<meta name="viewport" content="width=device-width, initial-scale=1">

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet"
>

<style>

body{
    background:#fff0f6;
    min-height:100vh;
}

.navbar{
    background:#4a1834;
}

.navbar-brand{
    color:white !important;
    font-weight:bold;
}

.card{
    border:none;
    border-radius:18px;
    box-shadow:0 8px 25px rgba(0,0,0,0.15);
}

.card-header{
    background:#d63384;
    color:white;
    font-size:24px;
    font-weight:bold;
    padding:20px;
}

.form-control{
    border-radius:10px;
}

.btn-register{
    background:#d63384;
    color:white;
    border:none;
}

.btn-register:hover{
    background:#b0256b;
    color:white;
}

.login-link{
    color:#d63384;
    font-weight:bold;
    text-decoration:none;
}

</style>

</head>

<body>


<!-- NAVBAR -->

<nav class="navbar">

<div class="container">

<a class="navbar-brand" href="index.php">
<?php echo mm_logo(""); ?>
</a>

<div>

<a href="index.php" class="btn btn-light btn-sm">
Home
</a>

<a href="login.php" class="btn btn-danger btn-sm">
Login
</a>

</div>

</div>

</nav>




<div class="container py-5">

<div class="row justify-content-center">

<div class="col-md-6">

<div class="card">

<div class="card-header text-center">

🎓 Student Registration

</div>

<div class="card-body p-4">

<?php echo $message; ?>


<form method="POST">


<div class="mb-3">

<label class="form-label">
Full Name
</label>

<input
type="text"
name="name"
class="form-control"
required
>

</div>


<div class="mb-3">

<label class="form-label">
Email
</label>

<input
type="email"
name="email"
class="form-control"
required
>

</div>


<div class="mb-3">

<label class="form-label">
Mobile
</label>

<input
type="text"
name="mobile"
class="form-control"
required
>

</div>


<div class="mb-3">

<label class="form-label">
College
</label>

<input
type="text"
name="college"
class="form-control"
required
>

</div>


<div class="mb-3">

<label class="form-label">
Password
</label>

<input
type="password"
name="password"
class="form-control"
required
>

</div>


<button
type="submit"
name="register"
class="btn btn-register w-100 py-2"
>

Create Account

</button>

</form>


<hr>


<div class="text-center">

Already have an account?

<a href="login.php" class="login-link">
Login Now
</a>

</div>


</div>

</div>

</div>

</div>

</div>

</body>

</html>