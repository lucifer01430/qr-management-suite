<?php
session_start();
session_unset();
session_destroy();

// Detect app path dynamically
$basePath = str_replace('\\', '/', dirname($_SERVER['PHP_SELF']));
$basePath = rtrim($basePath, '/');

// Redirect to index page after logout
header("Location: {$basePath}/index.php");
exit;
