<?php
session_start();
if (!isset($_SESSION['student_id'])) { header('Location: ../login.php'); exit(); }
include('../database/database.php');
$user_id = (int)$_SESSION['student_id'];
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['place_order'])) { header('Location: checkout.php'); exit(); }
$address = trim($_POST['address'] ?? '');
$payment_method = $_POST['payment_method'] ?? 'UPI';
if ($address === '' || strlen($address) > 1000 || !in_array($payment_method, ['UPI','Cash on Delivery'], true)) { die('Please enter a valid delivery address and payment method. <a href="checkout.php">Go back</a>'); }
$check = mysqli_prepare($conn, 'SELECT college_id_file, verification_status FROM users WHERE id=? LIMIT 1');
mysqli_stmt_bind_param($check, 'i', $user_id); mysqli_stmt_execute($check); $res = mysqli_stmt_get_result($check); $student = mysqli_fetch_assoc($res);
if (!$student || empty($student['college_id_file'])) { header('Location: dashboard.php?upload_required=1#college-id-verification'); exit(); }
$verified = (($student['verification_status'] ?? '') === 'Approved');
$cartq = mysqli_query($conn, "SELECT cart.food_id, cart.quantity, foods.price FROM cart INNER JOIN foods ON cart.food_id=foods.id WHERE cart.user_id=$user_id");
if (!$cartq || mysqli_num_rows($cartq) === 0) { header('Location: cart.php'); exit(); }
$items=[]; $subtotal=0;
while($item=mysqli_fetch_assoc($cartq)){ $item['food_id']=(int)$item['food_id']; $item['quantity']=(int)$item['quantity']; $item['price']=(float)$item['price']; $subtotal += $item['price']*$item['quantity']; $items[]=$item; }
$discount = $verified ? round($subtotal * 0.50, 2) : 0;
$total = max(0, $subtotal-$discount);
$address = mysqli_real_escape_string($conn, $address);
$paymentEsc = mysqli_real_escape_string($conn, $payment_method);
// Order remains Pending for the restaurant/admin. UPI intent links cannot confirm payment automatically.
mysqli_begin_transaction($conn);
try {
  $sql = "INSERT INTO orders (user_id,total_amount,address,payment_method,status) VALUES ($user_id,$total,'$address','$paymentEsc','Pending')";
  if (!mysqli_query($conn,$sql)) throw new Exception(mysqli_error($conn));
  $order_id = mysqli_insert_id($conn);
  foreach($items as $item){
    $stmt=mysqli_prepare($conn,'INSERT INTO order_items (order_id,food_id,quantity,price) VALUES (?,?,?,?)');
    mysqli_stmt_bind_param($stmt,'iiid',$order_id,$item['food_id'],$item['quantity'],$item['price']);
    if(!mysqli_stmt_execute($stmt)) throw new Exception(mysqli_error($conn));
  }
  if (!mysqli_query($conn,"DELETE FROM cart WHERE user_id=$user_id")) throw new Exception(mysqli_error($conn));
  mysqli_commit($conn);
  header('Location: orders_success.php?id='.$order_id.'&payment='.urlencode($payment_method)); exit();
} catch(Throwable $e) { mysqli_rollback($conn); http_response_code(500); echo 'Order could not be placed. Please check database tables and try again. <a href="checkout.php">Return to checkout</a>'; }
?>
