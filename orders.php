
<?php
require_once 'common.php';

$message = '';
$allowedStatuses = [
    'Pending',
    'Accepted',
    'Preparing',
    'Out for Delivery',
    'Delivered',
    'Cancelled'
];

// Update order status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id = (int)($_POST['order_id'] ?? 0);
    $newStatus = $_POST['order_status'] ?? '';

    if (in_array($newStatus, $allowedStatuses, true)) {
        // Ensure this order contains at least one item belonging to this vendor.
        $check = $conn->prepare(
            "SELECT COUNT(*) AS total
             FROM order_items oi
             JOIN foods f ON f.food_id = oi.food_id
             WHERE oi.order_id = ? AND f.vendor_id = ?"
        );
        $check->bind_param("ii", $order_id, $vendor_id);
        $check->execute();
        $ownsOrder = (int)$check->get_result()->fetch_assoc()['total'] > 0;
        $check->close();

        if ($ownsOrder) {
            $update = $conn->prepare(
                "UPDATE orders SET order_status = ? WHERE order_id = ?"
            );
            $update->bind_param("si", $newStatus, $order_id);

            if ($update->execute()) {
                $message = "Order status updated.";
            } else {
                $message = "Could not update order: " . $update->error;
            }
            $update->close();
        } else {
            $message = "You are not authorised to update this order.";
        }
    } else {
        $message = "Invalid order status.";
    }
}

// Fetch orders containing this vendor's food
$sql = "
SELECT
    o.order_id,
    o.order_status,
    o.total_amount,
    o.created_at,
    GROUP_CONCAT(
        CONCAT(f.food_name, ' x ', oi.quantity)
        SEPARATOR ', '
    ) AS items
FROM orders o
JOIN order_items oi ON oi.order_id = o.order_id
JOIN foods f ON f.food_id = oi.food_id
WHERE f.vendor_id = ?
GROUP BY o.order_id, o.order_status, o.total_amount, o.created_at
ORDER BY o.order_id DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $vendor_id);
$stmt->execute();
$orders = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Vendor Orders - MealMate</title>
<style>
body{font-family:Arial;background:#fff7f9;margin:0;padding:25px}
.wrap{max-width:1150px;margin:auto}
.box{background:white;padding:22px;border-radius:12px;overflow-x:auto;
box-shadow:0 3px 12px #0001}
table{width:100%;border-collapse:collapse;min-width:750px}
th,td{padding:13px;border-bottom:1px solid #eee;text-align:left}
select{padding:9px;border:1px solid #ddd;border-radius:6px}
button{background:#d63384;color:white;border:0;padding:9px 12px;
border-radius:6px;cursor:pointer}
.message{background:#fce4ef;padding:12px;border-radius:6px}
</style>
</head>
<body>
<div class="wrap">
<h1>Manage Orders</h1>
<p><a href="dashboard.php">← Back to Dashboard</a></p>

<?php if ($message): ?>
<p class="message"><?= h($message) ?></p>
<?php endif; ?>

<div class="box">
<table>
<tr>
    <th>Order ID</th>
    <th>Food Items</th>
    <th>Order Amount</th>
    <th>Date</th>
    <th>Status</th>
    <th>Update</th>
</tr>

<?php if ($orders->num_rows === 0): ?>
<tr><td colspan="6">No orders found.</td></tr>
<?php endif; ?>

<?php while ($order = $orders->fetch_assoc()): ?>
<tr>
    <td>#<?= (int)$order['order_id'] ?></td>
    <td><?= h($order['items']) ?></td>
    <td>₹<?= number_format((float)$order['total_amount'], 2) ?></td>
    <td><?= h($order['created_at']) ?></td>
    <td><?= h($order['order_status']) ?></td>
    <td>
        <form method="POST">
            <input type="hidden" name="order_id"
                   value="<?= (int)$order['order_id'] ?>">
            <select name="order_status">
                <?php foreach ($allowedStatuses as $status): ?>
                <option value="<?= h($status) ?>"
                    <?= $order['order_status'] === $status ? 'selected' : '' ?>>
                    <?= h($status) ?>
                </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" name="update_status">Update</button>
        </form>
    </td>
</tr>
<?php endwhile; ?>
</table>
</div>
</div>
</body>
</html>
