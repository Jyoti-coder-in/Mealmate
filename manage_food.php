
<?php
require_once __DIR__ . '/common.php';

$uploadDir = __DIR__ . '/../uploads/';

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0775, true);
}

$message = '';
$editFood = null;

// DELETE FOOD
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    $stmt = $conn->prepare(
        "DELETE FROM foods WHERE food_id = ? AND vendor_id = ?"
    );

    if (!$stmt) {
        die("Database error: " . $conn->error);
    }

    $stmt->bind_param("ii", $id, $vendor_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        header("Location: manage_food.php?msg=deleted");
        exit;
    }

    $message = "Food item not found or not owned by you.";
    $stmt->close();
}

// EDIT FOOD
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];

    $stmt = $conn->prepare(
        "SELECT * FROM foods WHERE food_id = ? AND vendor_id = ?"
    );

    $stmt->bind_param("ii", $id, $vendor_id);
    $stmt->execute();

    $editFood = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// ADD / UPDATE FOOD
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id = (int)($_POST['food_id'] ?? 0);
    $name = trim($_POST['food_name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $discount = (float)($_POST['discount'] ?? 0);
    $availability = $_POST['availability'] ?? 'Available';

    if (
        $name === '' ||
        $price <= 0 ||
        $discount < 0 ||
        $discount > 100 ||
        !in_array($availability, ['Available', 'Unavailable'], true)
    ) {
        $message = "Please enter valid food details.";
    } else {

        $imageName = '';
        $uploadError = '';

        // IMAGE UPLOAD
        if (
            isset($_FILES['image']) &&
            $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE
        ) {
            $file = $_FILES['image'];

            if ($file['error'] !== UPLOAD_ERR_OK ||
                $file['size'] > 5 * 1024 * 1024) {

                $uploadError = "Image must be under 5 MB.";

            } else {

                $allowed = [
                    'image/jpeg' => 'jpg',
                    'image/png' => 'png',
                    'image/webp' => 'webp'
                ];

                $mime = mime_content_type($file['tmp_name']);

                if (!isset($allowed[$mime])) {
                    $uploadError = "Only JPG, PNG and WEBP images allowed.";
                } else {
                    $imageName = uniqid('food_', true) . '.' . $allowed[$mime];

                    if (!move_uploaded_file(
                        $file['tmp_name'],
                        $uploadDir . $imageName
                    )) {
                        $uploadError = "Image upload failed.";
                        $imageName = '';
                    }
                }
            }
        }

        if ($uploadError !== '') {
            $message = $uploadError;
        } else {

            if ($id > 0) {

                if ($imageName !== '') {
                    $stmt = $conn->prepare(
                        "UPDATE foods SET
                        food_name=?, description=?, price=?,
                        discount=?, availability=?, image=?
                        WHERE food_id=? AND vendor_id=?"
                    );

                    $stmt->bind_param(
                        "ssddssii",
                        $name, $description, $price,
                        $discount, $availability, $imageName,
                        $id, $vendor_id
                    );

                } else {
                    $stmt = $conn->prepare(
                        "UPDATE foods SET
                        food_name=?, description=?, price=?,
                        discount=?, availability=?
                        WHERE food_id=? AND vendor_id=?"
                    );

                    $stmt->bind_param(
                        "ssddsii",
                        $name, $description, $price,
                        $discount, $availability,
                        $id, $vendor_id
                    );
                }

            } else {

                $stmt = $conn->prepare(
                    "INSERT INTO foods
                    (vendor_id, food_name, description, price,
                    discount, image, availability)
                    VALUES (?, ?, ?, ?, ?, ?, ?)"
                );

                $stmt->bind_param(
                    "issddss",
                    $vendor_id, $name, $description,
                    $price, $discount, $imageName, $availability
                );
            }

            if ($stmt && $stmt->execute()) {
                $stmt->close();
                header("Location: manage_food.php?msg=saved");
                exit;
            }

            $message = "Database error: " . $stmt->error;
            $stmt->close();
        }
    }
}

// FETCH VENDOR FOOD ITEMS
$stmt = $conn->prepare(
    "SELECT * FROM foods
     WHERE vendor_id = ?
     ORDER BY food_id DESC"
);

if (!$stmt) {
    die("Food table error: " . $conn->error .
        ". Check whether vendor_id exists in foods table.");
}

$stmt->bind_param("i", $vendor_id);
$stmt->execute();
$foods = $stmt->get_result();

$totalFoods = $foods->num_rows;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Manage Food Items - MealMate</title>

<style>
*{box-sizing:border-box}

body{
    margin:0;
    font-family:Arial,sans-serif;
    background:#fff5f8;
    color:#333;
}

.header{
    background:#29232a;
    color:white;
    padding:20px 30px;
}

.header h2{
    color:#ff91bd;
    margin:0;
}

.container{
    max-width:1200px;
    margin:auto;
    padding:25px;
}

.box{
    background:white;
    padding:25px;
    border-radius:12px;
    margin-bottom:25px;
    box-shadow:0 3px 12px #0001;
}

h1,h2{
    color:#d63384;
}

input,textarea,select{
    width:100%;
    padding:12px;
    border:1px solid #ddd;
    border-radius:7px;
    margin:8px 0 16px;
}

textarea{
    min-height:90px;
}

button,.btn{
    background:#d63384;
    color:white;
    padding:12px 18px;
    border:0;
    border-radius:7px;
    cursor:pointer;
    text-decoration:none;
    display:inline-block;
}

.cancel{
    background:#777;
}

.delete{
    background:#dc3545;
}

.edit{
    background:#198754;
}

.grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:15px;
}

table{
    width:100%;
    border-collapse:collapse;
}

th,td{
    padding:13px;
    border-bottom:1px solid #eee;
    text-align:left;
}

th{
    background:#fff0f5;
}

.food-img{
    width:70px;
    height:60px;
    object-fit:cover;
    border-radius:7px;
}

.status{
    color:#168044;
    font-weight:bold;
}

.unavailable{
    color:#dc3545;
}

.message{
    background:#fce4ef;
    padding:13px;
    border-radius:7px;
    margin-bottom:15px;
}

@media(max-width:700px){
    .grid{grid-template-columns:1fr}
    .container{padding:12px}
    .table-wrap{overflow-x:auto}
    table{min-width:750px}
}
</style>
</head>

<body>

<div class="header">
    <h2>MealMate Vendor Panel</h2>
    <p>Manage Your Food Items</p>
</div>

<div class="container">

    <p>
        <a class="btn cancel" href="dashboard.php">
            ← Back to Dashboard
        </a>
    </p>

    <h1>Manage Food Items</h1>

    <div class="box">
        <h2>Your Food Items: <?= $totalFoods ?></h2>
    </div>

    <?php if (isset($_GET['msg'])): ?>
        <div class="message">
            <?php
            if ($_GET['msg'] === 'saved') echo "Food item saved successfully!";
            if ($_GET['msg'] === 'deleted') echo "Food item deleted successfully!";
            ?>
        </div>
    <?php endif; ?>

    <?php if ($message): ?>
        <div class="message">
            <?= h($message) ?>
        </div>
    <?php endif; ?>

    <!-- ADD / EDIT FORM -->

    <div class="box">

        <h2>
            <?= $editFood ? 'Edit Food Item' : 'Add New Food Item' ?>
        </h2>

        <form method="POST" enctype="multipart/form-data">

            <input type="hidden"
                   name="food_id"
                   value="<?= (int)($editFood['food_id'] ?? 0) ?>">

            <div class="grid">

                <div>
                    <label>Food Name</label>
                    <input type="text"
                           name="food_name"
                           required
                           value="<?= h($editFood['food_name'] ?? '') ?>">
                </div>

                <div>
                    <label>Price (₹)</label>
                    <input type="number"
                           name="price"
                           min="0.01"
                           step="0.01"
                           required
                           value="<?= h($editFood['price'] ?? '') ?>">
                </div>

                <div>
                    <label>Discount (%)</label>
                    <input type="number"
                           name="discount"
                           min="0"
                           max="100"
                           step="0.01"
                           value="<?= h($editFood['discount'] ?? 0) ?>">
                </div>

                <div>
                    <label>Availability</label>

                    <select name="availability">
                        <option value="Available"
                        <?= ($editFood['availability'] ?? 'Available') === 'Available' ? 'selected' : '' ?>>
                            Available
                        </option>

                        <option value="Unavailable"
                        <?= ($editFood['availability'] ?? '') === 'Unavailable' ? 'selected' : '' ?>>
                            Unavailable
                        </option>
                    </select>
                </div>

            </div>

            <label>Description</label>

            <textarea name="description"><?= h($editFood['description'] ?? '') ?></textarea>

            <label>Food Image</label>

            <input type="file"
                   name="image"
                   accept=".jpg,.jpeg,.png,.webp">

            <button type="submit">
                <?= $editFood ? 'Update Food Item' : 'Add Food Item' ?>
            </button>

            <?php if ($editFood): ?>
                <a class="btn cancel" href="manage_food.php">
                    Cancel
                </a>
            <?php endif; ?>

        </form>

    </div>

    <!-- FOOD LIST -->

    <div class="box">

        <h2>All Your Food Items</h2>

        <div class="table-wrap">

            <table>

                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Food Name</th>
                        <th>Price</th>
                        <th>Discount</th>
                        <th>Availability</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>

                <?php if ($foods->num_rows > 0): ?>

                    <?php while ($food = $foods->fetch_assoc()): ?>

                    <tr>

                        <td>
                            <?php if (!empty($food['image'])): ?>
                                <img class="food-img"
                                     src="../uploads/<?= rawurlencode($food['image']) ?>"
                                     alt="Food">
                            <?php else: ?>
                                No Image
                            <?php endif; ?>
                        </td>

                        <td><?= h($food['food_name']) ?></td>

                        <td>₹<?= number_format((float)$food['price'],2) ?></td>

                        <td><?= h($food['discount']) ?>%</td>

                        <td>
                            <span class="<?= $food['availability'] === 'Available' ? 'status' : 'unavailable' ?>">
                                <?= h($food['availability']) ?>
                            </span>
                        </td>

                        <td>
                            <a class="btn edit"
                               href="?edit=<?= (int)$food['food_id'] ?>">
                                Edit
                            </a>

                            <a class="btn delete"
                               href="?delete=<?= (int)$food['food_id'] ?>"
                               onclick="return confirm('Delete this food item?')">
                                Delete
                            </a>
                        </td>

                    </tr>

                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="6">No food items added yet.</td>
                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

</body>
</html>
