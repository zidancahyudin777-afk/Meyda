<?php
if(session_status() === PHP_SESSION_NONE) session_start();

function requireAdmin() {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        header("Location: authmasuk.php");
        exit();
    }
}

function requireUser() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: authmasuk.php");
        exit();
    }
}
?>
