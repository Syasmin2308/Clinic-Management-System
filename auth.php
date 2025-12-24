<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn() {
    return isset($_SESSION['user']);
}

function requireAuth() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

function getUserRole() {
    return $_SESSION['user']['role'] ?? null;
}

function getUserName() {
    return $_SESSION['user']['name'] ?? null;
}
?>