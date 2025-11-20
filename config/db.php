<?php
// config/db.php

// ====== EDIT THESE FOR HOSTINGER ======
const DB_HOST = 'localhost';
const DB_NAME = 'qrportal';
const DB_USER = 'root';
const DB_PASS = '';   // (empty by default in XAMPP)


// Optional: app timezone
date_default_timezone_set('Asia/Kolkata');

// Start session globally (include this file early)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Returns a singleton PDO connection
 */
function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4';
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // In production, avoid echoing raw errors. For now, simple message:
            exit('Database connection failed.');
        }
    }
    return $pdo;
}

/**
 * Simple helper to check login; use in pages that require auth
 */
function require_login(): void {
    if (empty($_SESSION['user_id'])) {
        header('Location: /login.php');
        exit;
    }
}

/**
 * Check if current user is admin
 */
function is_admin(): bool {
    return !empty($_SESSION['role']) && $_SESSION['role'] === 'admin';
}
