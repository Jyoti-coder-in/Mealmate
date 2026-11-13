
<?php
session_start();

require_once __DIR__ . '/../database/database.php';

// Admin login protection
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$message = '';
$messageType = 'success';

// Add new vendor
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_vendor'])) {

    $vendor_name = trim($_POST['vendor_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($vendor_name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid vendor name and email.";
        $messageType = 'error';

    } elseif (strlen($password) < 6) {
        $message = "Password must be at least 6 characters.";
        $messageType = 'error';

    } else {
        // Check duplicate email
        $check = $conn->prepare(
            "SELECT vendor_id FROM vendors WHERE email = ? LIMIT 1"
        );

        if (!$check) {
            $message = "Database error: " . $conn->error;
            $messageType = 'error';
        } else {
            $check->bind_param("s", $email);
            $check->execute();
            $exists = $check->get_result()->num_rows > 0;
            $check->close();

            if ($exists) {
                $message = "This vendor email is already registered.";
                $messageType = 'error';
            } else {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $status = 'Active';

                $insert = $conn->prepare(
                    "INSERT INTO vendors
                    (vendor_name, email, password, status)
                    VALUES (?, ?, ?, ?)"
                );

                if (!$insert) {
                    $message = "Database error: " . $conn->error;
                    $messageType = 'error';
                } else {
                    $insert->bind_param(
                        "ssss",
                        $vendor_name,
                        $email,
                        $hashedPassword,
                        $status
                    );

                    if ($insert->execute()) {
                        $message = "Vendor account created successfully!";
                    } else {
                        $message = "Unable to create vendor: " . $insert->error;
                        $messageType = 'error';
                    }

                    $insert->close();
                }
            }
        }
    }
}

// Activate / Deactivate vendor
if (isset($_GET['toggle'])) {
    $id = (int) $_GET['toggle'];

    $stmt = $conn->prepare(
        "UPDATE vendors
         SET status = CASE
             WHEN status = 'Active' THEN 'Inactive'
             ELSE 'Active'
         END
         WHERE vendor_id = ?"
    );

    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    header("Location: manage_vendors_discounts.php");
    exit;
}

// Delete vendor
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];

    $stmt = $conn->prepare(
        "DELETE FROM vendors WHERE vendor_id = ?"
    );

    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $stmt->close();
        header("Location: manage_vendors_discounts.php");
        exit;
    } else {
        $message = "Vendor could not be deleted. They may have existing food items or orders.";
        $messageType = 'error';
        $stmt->close();
    }
}

// Fetch vendors
$vendors = $conn->query(
    "SELECT vendor_id, vendor_name, email, status
     FROM vendors
     ORDER BY vendor_id DESC"
);

$totalVendors = $conn->query(
    "SELECT COUNT(*) AS total FROM vendors"
)->fetch_assoc()['total'] ?? 0;

$activeVendors = $conn->query(
    "SELECT COUNT(*) AS total FROM vendors WHERE status = 'Active'"
)->fetch_assoc()['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Manage Vendors - MealMate Admin</title>

<style>
* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: Arial, sans-serif;
    background: #fff5f8;
    color: #333;
}

.sidebar {
    width: 230px;
    height: 100vh;
    background: #29232a;
    position: fixed;
    top: 0;
    left: 0;
    padding: 25px 15px;
}

.sidebar h2 {
    color: #ff91bd;
    text-align: center;
}

.sidebar a {
    display: block;
    color: white;
    padding: 13px;
    text-decoration: none;
    border-radius: 6px;
    margin: 7px 0;
}

.sidebar a:hover,
.sidebar a.active {
    background: #d63384;
}

.main {
    margin-left: 230px;
    padding: 30px;
}

h1 {
    color: #d63384;
}

.stats {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
    margin: 20px 0;
}

.stat-card {
    background: white;
    padding: 22px;
    border-radius: 10px;
    min-width: 180px;
    flex: 1;
    box-shadow: 0 3px 12px #0000000d;
}

.stat-card h3 {
    color: #777;
    font-size: 15px;
}

.stat-card strong {
    font-size: 28px;
    color: #d63384;
}

.box {
    background: white;
    padding: 25px;
    border-radius: 12px;
    margin-top: 22px;
    box-shadow: 0 3px 12px #0000000d;
}

.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}

label {
    display: block;
    margin-bottom: 7px;
    font-weight: bold;
}

input {
    width: 100%;
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 7px;
    margin-bottom: 15px;
}

button {
    background: #d63384;
    color: white;
    border: 0;
    padding: 12px 22px;
    border-radius: 7px;
    cursor: pointer;
    font-weight: bold;
}

button:hover {
    background: #b9236b;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
}

th, td {
    text-align: left;
    padding: 14px 10px;
    border-bottom: 1px solid #eee;
}

th {
    background: #fff0f5;
}

.status {
    padding: 6px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: bold;
}

.active-status {
    background: #d9f7e5;
    color: #168044;
}

.inactive-status {
    background: #ffe0e0;
    color: #b42318;
}

.action {
    text-decoration: none;
    margin-right: 8px;
    font-size: 13px;
    font-weight: bold;
}

.toggle {
    color: #147a45;
}

.delete {
    color: #d63333;
}

.message {
    padding: 13px;
    border-radius: 7px;
    margin: 15px 0;
}

.success {
    background: #d9f7e5;
    color: #146c43;
}

.error {
    background: #ffe0e0;
    color: #b42318;
}

@media(max-width:700px) {
    .sidebar {
        width: 100%;
        height: auto;
        position: relative;
    }

    .main {
        margin-left: 0;
        padding: 15px;
    }

    .form-grid {
        grid-template-columns: 1fr;
    }

    .table-wrap {
        overflow-x: auto;
    }
}
</style>
</head>

<body>

<aside class="sidebar">
    <h2>MealMate Admin</h2>

    <a href="dashboard.php">Dashboard</a>
    <a href="manage_food.php">Manage Food</a>
    <a href="manage_restaurants.php">Manage Restaurants</a>
    <a href="manage_vendors_discounts.php" class="active">
        Manage Vendors & Discounts
    </a>
    <a href="logout.php">Logout</a>
</aside>

<main class="main">

    <h1>Manage Vendors</h1>
    <p>Add and manage restaurant vendors on MealMate.</p>

    <?php if ($message): ?>
        <div class="message <?= $messageType ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <div class="stats">

        <div class="stat-card">
            <h3>Total Vendors</h3>
            <strong><?= (int)$totalVendors ?></strong>
        </div>

        <div class="stat-card">
            <h3>Active Vendors</h3>
            <strong><?= (int)$activeVendors ?></strong>
        </div>

    </div>

    <!-- ADD VENDOR FORM -->
    <section class="box">

        <h2>Add New Vendor</h2>

        <form method="POST">

            <div class="form-grid">

                <div>
                    <label>Vendor / Restaurant Owner Name</label>
                    <input
                        type="text"
                        name="vendor_name"
                        placeholder="Enter vendor name"
                        required
                    >
                </div>

                <div>
                    <label>Vendor Email</label>
                    <input
                        type="email"
                        name="email"
                        placeholder="Enter vendor email"
                        required
                    >
                </div>

                <div>
                    <label>Login Password</label>
                    <input
                        type="password"
                        name="password"
                        placeholder="Minimum 6 characters"
                        minlength="6"
                        required
                    >
                </div>

            </div>

            <button type="submit" name="add_vendor">
                + Create Vendor Account
            </button>

        </form>

    </section>

    <!-- VENDOR LIST -->
    <section class="box">

        <h2>Registered Vendors</h2>

        <div class="table-wrap">

            <table>

                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Vendor Name</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>

                <?php if ($vendors && $vendors->num_rows > 0): ?>

                    <?php while ($vendor = $vendors->fetch_assoc()): ?>

                    <tr>

                        <td><?= (int)$vendor['vendor_id'] ?></td>

                        <td>
                            <?= htmlspecialchars($vendor['vendor_name']) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($vendor['email']) ?>
                        </td>

                        <td>
                            <?php if ($vendor['status'] === 'Active'): ?>
                                <span class="status active-status">Active</span>
                            <?php else: ?>
                                <span class="status inactive-status">Inactive</span>
                            <?php endif; ?>
                        </td>

                        <td>
                            <a class="action toggle"
                               href="?toggle=<?= (int)$vendor['vendor_id'] ?>"
                               onclick="return confirm('Change vendor status?')">
                                Activate / Deactivate
                            </a>

                            <a class="action delete"
                               href="?delete=<?= (int)$vendor['vendor_id'] ?>"
                               onclick="return confirm('Delete this vendor?')">
                                Delete
                            </a>
                        </td>

                    </tr>

                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="5">No vendors registered yet.</td>
                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </section>

</main>

</body>
</html>
