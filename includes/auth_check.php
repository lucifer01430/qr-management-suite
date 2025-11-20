<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';

// Detect base path dynamically (e.g., /qr-portal)
$basePath = str_replace('\\', '/', dirname($_SERVER['PHP_SELF']));
$basePath = rtrim($basePath, '/');

// Redirect unauthenticated users to login
if (empty($_SESSION['user_id'])) {
    header("Location: {$basePath}/login.php");
    exit;
}
