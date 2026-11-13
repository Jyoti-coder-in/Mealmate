
<?php
session_start();
require_once __DIR__ . '/../database/database.php';

$error = '';

if (isset($_SESSION['vendor_id'])) {
    header("Location: dashboard.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare(
        "SELECT vendor_id, vendor_name, email, password, status
         FROM vendors WHERE email = ? LIMIT 1"
    );

    if (!$stmt) {
        $error = "Database error: " . $conn->error;
    } else {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $vendor = $result->fetch_assoc();
        $stmt->close();

        if (
            $vendor &&
            password_verify($password, $vendor['password']) &&
            strtolower($vendor['status']) === 'active'
        ) {
            session_regenerate_id(true);

            $_SESSION['vendor_id'] = $vendor['vendor_id'];
            $_SESSION['vendor_name'] = $vendor['vendor_name'];
            $_SESSION['vendor_email'] = $vendor['email'];

            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Invalid email/password or inactive account.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Vendor Login - MealMate</title>
<style>
body{font-family:Arial;background:#fff3f7;display:flex;
justify-content:center;align-items:center;height:100vh;margin:0}
.login{background:white;padding:30px;width:340px;border-radius:12px;
box-shadow:0 3px 15px #0002}
h2{text-align:center;color:#d63384}
input{width:100%;padding:12px;margin:10px 0;
border:1px solid #ddd;border-radius:6px;box-sizing:border-box}
button{width:100%;padding:12px;background:#d63384;color:white;
border:0;border-radius:6px;cursor:pointer}
.error{color:red;text-align:center}
</style>
</head>
<body>
<div class="login">
    <h2>Vendor Login</h2>

    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST">
        <input type="email" name="email" placeholder="Vendor Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>
</div>
</body>
</html>
