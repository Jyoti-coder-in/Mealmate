
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../database/database.php';

if (!isset($_SESSION['vendor_id'])) {
    header("Location: login.php");
    exit;
}

$vendor_id = (int) $_SESSION['vendor_id'];

function h($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
?>
