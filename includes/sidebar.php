<?php
$pdo = db();
$stmt = $pdo->prepare("SELECT name, profile_pic FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$userData = $stmt->fetch();
$avatar = (!empty($userData['profile_pic']) && file_exists(__DIR__ . '/../' . $userData['profile_pic']))
  ? $userData['profile_pic']
  : 'assets/default-avatar.png';
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<aside class="main-sidebar sidebar-light-primary elevation-4">
  <a href="dashboard.php" class="brand-link text-center py-3">
    <span class="brand-text font-weight-bold">QR Portal</span>
  </a>
  <div class="sidebar">
    <!-- User Info -->
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
      <div class="image">
        <img src="<?= $avatar ?>" class="img-circle elevation-2" alt="User Image" width="40" height="40">
      </div>
      <div class="info">
        <a href="profile.php" class="d-block fw-semibold"><?= htmlspecialchars($_SESSION['user_name']); ?></a>
      </div>
    </div>

    <!-- Navigation -->
    <nav class="mt-3">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview">
        <li class="nav-item">
          <a href="dashboard.php" class="nav-link<?= $currentPage === 'dashboard.php' ? ' active' : '' ?>">
            <i class="nav-icon fas fa-home"></i>
            <p>Dashboard</p>
          </a>
        </li>
        <?php if ($_SESSION['role'] === 'admin'): ?>
        <li class="nav-item">
          <a href="admin_dashboard.php" class="nav-link<?= $currentPage === 'admin_dashboard.php' ? ' active' : '' ?>">
            <i class="nav-icon fas fa-user-shield"></i>
            <p>Admin panel</p>
          </a>
        </li>
        <?php endif; ?>

        <li class="nav-item">
          <a href="upload.php" class="nav-link<?= $currentPage === 'upload.php' ? ' active' : '' ?>">
            <i class="nav-icon fas fa-file-upload"></i>
            <p>Upload PDF</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="history.php" class="nav-link<?= $currentPage === 'history.php' ? ' active' : '' ?>">
            <i class="nav-icon fas fa-clock"></i>
            <p>My uploads</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="logout.php" class="nav-link text-danger">
            <i class="nav-icon fas fa-sign-out-alt"></i>
            <p>Logout</p>
          </a>
        </li>
      </ul>
    </nav>
  </div>
</aside>
