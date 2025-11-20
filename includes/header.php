<?php
if (session_status() === PHP_SESSION_NONE) session_start();

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>QR Portal Dashboard</title>
  <meta name="description" content="Manage secure QR codes, upload documents, and track activity seamlessly with the QR Portal dashboard." />
  <meta name="keywords" content="QR Portal, QR code dashboard, document management, secure QR generator, upload PDFs, QR Code, Portal" />
  <meta name="author" content="Harsh Pandey" />
  <meta name="robots" content="index, follow" />
  <meta property="og:site_name" content="QR Portal" />
  <meta property="og:type" content="website" />
  <meta property="og:title" content="QR Portal Dashboard" />
  <meta property="og:description" content="Generate QR codes from PDFs, monitor uploads, and stay in control of your sharing workflow." />
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:title" content="QR Portal Dashboard" />
  <meta name="twitter:description" content="Upload, generate, and manage QR codes with a modern, secure dashboard experience." />

  <!-- Bootstrap 5 -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <!-- AdminLTE -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">

  <!-- Custom CSS -->
  <link rel="stylesheet" href="assets/css/style.css">

<link rel="icon" type="image/png" href="assets/favicon/favicon-96x96.png" sizes="96x96" />
<link rel="icon" type="image/svg+xml" href="assets/favicon/favicon.svg" />
<link rel="shortcut icon" href="assets/favicon/favicon.ico" />
<link rel="apple-touch-icon" sizes="180x180" href="assets/favicon/apple-touch-icon.png" />
<meta name="apple-mobile-web-app-title" content="Qr-Portal" />
<link rel="manifest" href="assets/favicon/site.webmanifest" />

</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

<!-- Main Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light border-bottom shadow-sm">
  <!-- Left navbar links -->
  <ul class="navbar-nav">
    <li class="nav-item">
      <!-- Sidebar toggle button -->
      <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
    </li>
    <li class="nav-item d-none d-sm-inline-block">
      <a href="dashboard.php" class="nav-link">Home</a>
    </li>
  </ul>

  <!-- Right navbar links -->
  <ul class="navbar-nav ms-auto align-items-center">
    <li class="nav-item me-2">
      <button type="button" data-theme-toggle class="btn btn-sm btn-outline-secondary rounded-pill d-flex align-items-center gap-2">
        <i class="fas fa-moon"></i>
        <span data-theme-label class="d-none d-md-inline">Dark mode</span>
      </button>
    </li>
    <li class="nav-item">
      <a href="logout.php" class="btn btn-sm btn-outline-danger d-flex align-items-center gap-2">
        <i class="fas fa-sign-out-alt"></i>
        <span class="d-none d-md-inline">Logout</span>
      </a>
    </li>
  </ul>
</nav>

