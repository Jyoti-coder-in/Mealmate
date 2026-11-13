
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);


session_start();

if (!isset($_SESSION['student_id'])) {
    header("Location: ../login.php");
    exit();
}

require_once __DIR__ . "/../includes/helpers.php";
require_once __DIR__ . "/../database/database.php";

$user_id = (int) $_SESSION['student_id'];

// Fetch student verification details
$stmt = mysqli_prepare(
    $conn,
    "SELECT college_id_file, verification_status
     FROM users WHERE id = ? LIMIT 1"
);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$student = mysqli_fetch_assoc($result);

if (!$student || empty($student['college_id_file'])) {
    header("Location: dashboard.php?upload_required=1#college-id-verification");
    exit();
}

$verified = ($student['verification_status'] === 'Approved');

// Fetch cart items
$stmt = mysqli_prepare(
    $conn,
    "SELECT c.id, c.food_id, c.quantity,
            f.food_name, f.price, f.image
     FROM cart c
     INNER JOIN foods f ON c.food_id = f.id
     WHERE c.user_id = ?"
);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$items = [];
$subtotal = 0;

while ($row = mysqli_fetch_assoc($result)) {
    $row['quantity'] = max(1, (int)$row['quantity']);
    $row['price'] = (float)$row['price'];
    $row['line_total'] = $row['price'] * $row['quantity'];

    $subtotal += $row['line_total'];
    $items[] = $row;
}

$discount = $verified ? round($subtotal * 0.50, 2) : 0;
$total = max(0, $subtotal - $discount);

// Merchant UPI ID: verify this before accepting payments.
$upi_id = "8744851505@ybl";

$upi_link = "upi://pay?pa=" . urlencode($upi_id)
    . "&pn=" . urlencode("MealMate")
    . "&am=" . urlencode(number_format($total, 2, '.', ''))
    . "&cu=INR";

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>MealMate - Checkout</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    background: #fff0f6;
    color: #4a1834;
    font-family: Arial, sans-serif;
}

.navbar {
    background: #4a1834;
}

.heading {
    color: #d63384;
    font-weight: 800;
}

.checkout-card {
    background: white;
    border: none;
    border-radius: 18px;
    padding: 25px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    margin-bottom: 22px;
}

.btn-pink {
    background: #d63384;
    color: white;
    border: none;
    border-radius: 9px;
    padding: 12px;
    font-weight: bold;
}

.btn-pink:hover {
    background: #b0256b;
    color: white;
}

.food-image {
    width: 65px;
    height: 60px;
    object-fit: cover;
    border-radius: 10px;
}

.total {
    color: #d63384;
    font-size: 25px;
    font-weight: bold;
}

.upi-box {
    background: #f2f8ff;
    border: 1px solid #b6d9f5;
    border-radius: 12px;
    padding: 18px;
}

.form-control {
    border-radius: 9px;
}

</style>
</head>

<body>

<nav class="navbar navbar-dark">
<div class="container py-2">

<a class="navbar-brand" href="../index.php">
<?php echo mm_logo("../"); ?>
</a>

<div>
<a href="foods.php" class="btn btn-light btn-sm">Food Menu</a>
<a href="cart.php" class="btn btn-warning btn-sm">My Cart</a>
</div>

</div>
</nav>

<div class="container py-5">

<h2 class="heading mb-4">MealMate Checkout</h2>

<?php if (empty($items)): ?>

<div class="checkout-card text-center">
<h4>Your Cart Is Empty!</h4>
<p>Please add food items before checkout.</p>
<a href="foods.php" class="btn btn-pink">Browse Food</a>
</div>

<?php else: ?>

<div class="row g-4">

<!-- LEFT SECTION -->

<div class="col-lg-7">

<div class="checkout-card">

<h4 class="heading">Your Order</h4>
<hr>

<?php foreach ($items as $item): ?>

<div class="d-flex align-items-center gap-3 mb-3">

<?php if (!empty($item['image'])): ?>

<img class="food-image"
src="../uploads/<?php echo e(basename($item['image'])); ?>"
alt="<?php echo e($item['food_name']); ?>">

<?php else: ?>

<div class="food-image bg-light d-flex align-items-center justify-content-center">
🍽️
</div>

<?php endif; ?>

<div class="flex-grow-1">

<strong><?php echo e($item['food_name']); ?></strong>

<div class="text-muted small">
₹<?php echo number_format($item['price'], 2); ?>
× <?php echo $item['quantity']; ?>
</div>

</div>

<strong>
₹<?php echo number_format($item['line_total'], 2); ?>
</strong>

</div>

<?php endforeach; ?>

</div>

<!-- STUDENT DISCOUNT -->

<div class="checkout-card">

<h4 class="heading">Student Discount</h4>

<?php if ($verified): ?>

<div class="alert alert-success mb-0">
Congratulations! Your College ID is approved.
50% student discount has been applied.
</div>

<?php else: ?>

<div class="alert alert-info mb-0">
Your College ID is uploaded.

Status:
<strong><?php echo e($student['verification_status'] ?? 'Pending'); ?></strong>.

50% discount will be applied after Admin approval.
</div>

<?php endif; ?>

</div>

<!-- DELIVERY AND PAYMENT -->

<div class="checkout-card">

<h4 class="heading">Delivery Details</h4>

<form method="POST" action="place_order.php">

<div class="mb-3">

<label class="form-label fw-bold">
Delivery Address
</label>

<textarea
name="address"
class="form-control"
rows="3"
maxlength="1000"
required
placeholder="Enter hostel, room number, building and complete address"></textarea>

</div>

<h5 class="heading mt-4">Payment Method</h5>

<div class="form-check mb-3">

<input
class="form-check-input"
type="radio"
name="payment_method"
value="UPI"
id="upi"
checked>

<label class="form-check-label" for="upi">
UPI Payment
</label>

</div>

<div class="upi-box mb-3" id="upiBox">

<strong>UPI ID:</strong>
<?php echo e($upi_id); ?>

<br><br>

<strong>Amount:</strong>
₹<?php echo number_format($total, 2); ?>

<a href="<?php echo e($upi_link); ?>"
class="btn btn-success w-100 mt-3">
Pay Using UPI App
</a>

<small class="text-muted d-block mt-2">
Payment is not automatically verified. Confirm the transaction independently before treating it as paid.
</small>

</div>

<div class="form-check mb-4">

<input
class="form-check-input"
type="radio"
name="payment_method"
value="Cash on Delivery"
id="cod">

<label class="form-check-label" for="cod">
Cash on Delivery
</label>

</div>

<button
type="submit"
name="place_order"
value="1"
class="btn btn-pink w-100">

Place Order - ₹<?php echo number_format($total, 2); ?>

</button>

</form>

</div>

</div>

<!-- RIGHT SECTION -->

<div class="col-lg-5">

<div class="checkout-card">

<h4 class="heading">Payment Summary</h4>

<hr>

<div class="d-flex justify-content-between mb-3">
<span>Subtotal</span>
<strong>₹<?php echo number_format($subtotal, 2); ?></strong>
</div>

<?php if ($discount > 0): ?>

<div class="d-flex justify-content-between text-success mb-3">
<span>Student Discount (50%)</span>
<strong>- ₹<?php echo number_format($discount, 2); ?></strong>
</div>

<?php endif; ?>

<hr>

<div class="d-flex justify-content-between align-items-center">

<strong>Total Amount</strong>

<span class="total">
₹<?php echo number_format($total, 2); ?>
</span>

</div>

<a href="cart.php" class="btn btn-outline-secondary w-100 mt-4">
Edit Cart
</a>

</div>

</div>

</div>

<?php endif; ?>

</div>

<script>

const upi = document.getElementById('upi');
const cod = document.getElementById('cod');
const upiBox = document.getElementById('upiBox');

function updatePayment() {
    if (upiBox) {
        upiBox.style.display = upi.checked ? 'block' : 'none';
    }
}

if (upi) upi.addEventListener('change', updatePayment);
if (cod) cod.addEventListener('change', updatePayment);

updatePayment();

</script>

</body>
</html>