<?php
/** Shared session and role guards. Include before output when used. */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function mm_require_student(): void {
    if (empty($_SESSION['student_id'])) {
        header('Location: ../login.php');
        exit;
    }
}

function mm_require_admin(): void {
    if (empty($_SESSION['admin_id'])) {
        header('Location: login.php');
        exit;
    }
}

function mm_require_vendor(): void {
    if (empty($_SESSION['vendor_id'])) {
        header('Location: login.php');
        exit;
    }
}
