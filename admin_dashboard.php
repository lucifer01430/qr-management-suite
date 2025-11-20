<?php
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/config/db.php';
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/sidebar.php';

if ($_SESSION['role'] !== 'admin') {
  echo '<div class="content-wrapper px-4 py-5 content-page"><div class="container-fluid"><div class="card border-0 shadow-sm text-center p-5"><i class="fas fa-ban fa-3x text-danger mb-3"></i><h3 class="fw-bold mb-2">Access denied</h3><p class="text-muted mb-0">You do not have administrator permissions.</p></div></div></div>';
  include __DIR__ . '/includes/footer.php';
  exit;
}

$pdo = db();
$msg = null;
$msgType = 'info';
$msgTitle = 'Heads up';

// Handle delete user action
if (isset($_GET['delete_user'])) {
  $delete_id = intval($_GET['delete_user']);
  if ($delete_id !== $_SESSION['user_id']) {
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$delete_id]);
    $msg = "User deleted successfully!";
    $msgType = 'success';
    $msgTitle = 'Action completed';
  } else {
    $msg = "You cannot delete your own account.";
    $msgType = 'error';
    $msgTitle = 'Action blocked';
  }
}

// Handle edit user (update role)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_user'])) {
  $uid = intval($_POST['user_id']);
  $role = $_POST['role'];
  $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
  $stmt->execute([$role, $uid]);
  $msg = "User role updated successfully!";
  $msgType = 'success';
  $msgTitle = 'Action completed';
}

// Fetch users and uploads
$users = $pdo->query("SELECT id, name, email, created_at, role FROM users ORDER BY created_at DESC")->fetchAll();
$uploads = $pdo->query("
  SELECT u.id, u.file_name, u.file_url, u.created_at, us.name AS user_name, us.email
  FROM uploads u
  JOIN users us ON u.user_id = us.id
  ORDER BY u.created_at DESC
")->fetchAll();
?>

<div class="content-wrapper px-4 py-3 content-page">
  <section class="content-header">
    <div class="container-fluid">
      <h3 class="fw-bold mb-1"><i class="fas fa-user-shield text-primary me-2"></i>Admin dashboard</h3>
      <p class="text-muted mb-0">Oversee user access and manage uploaded documents.</p>
    </div>
  </section>

  <section class="content">
    <div class="container-fluid">

      <?php if (!empty($msg)): ?>
        <div class="d-none"
             data-flash-message="<?= htmlspecialchars($msg, ENT_QUOTES) ?>"
             data-flash-type="<?= htmlspecialchars($msgType, ENT_QUOTES) ?>"
             data-flash-title="<?= htmlspecialchars($msgTitle, ENT_QUOTES) ?>"></div>
      <?php endif; ?>

      <div class="row g-4">
        <div class="col-12">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent border-0 pb-0 d-flex flex-wrap justify-content-between align-items-center gap-3">
              <h5 class="mb-0 fw-semibold text-primary"><i class="fas fa-users me-2"></i>Registered users</h5>
              <span class="badge bg-primary-subtle text-primary fw-semibold px-3 py-2">Total: <?= count($users) ?></span>
            </div>
            <div class="card-body table-responsive">
              <table class="table table-hover align-middle text-nowrap mb-0">
                <thead class="table-light">
                  <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Joined</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($users as $user): ?>
                    <tr>
                      <td><?= $user['id'] ?></td>
                      <td class="text-start">
                        <span class="fw-semibold"><?= htmlspecialchars($user['name']) ?></span>
                      </td>
                      <td class="text-start"><?= htmlspecialchars($user['email']) ?></td>
                      <td>
                        <form method="POST" class="d-inline-flex align-items-center gap-2">
                          <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                          <select name="role" class="form-select form-select-sm shadow-none" style="width: 130px;">
                            <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>User</option>
                            <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                          </select>
                          <button type="submit" name="edit_user" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-save"></i>
                          </button>
                        </form>
                      </td>
                      <td><?= date('d M Y', strtotime($user['created_at'])) ?></td>
                      <td>
                        <?php if ($user['id'] !== $_SESSION['user_id']): ?>
                          <a href="?delete_user=<?= $user['id'] ?>"
                             class="btn btn-sm btn-outline-danger"
                             data-confirm="This will permanently remove the user and their uploads."
                             data-confirm-title="Delete user?"
                             data-confirm-button="Delete"
                             data-confirm-icon="error">
                             <i class="fas fa-trash-alt"></i>
                          </a>
                        <?php else: ?>
                          <span class="badge bg-secondary-subtle text-secondary">You</span>
                        <?php endif; ?>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <div class="col-12">
          <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 pb-0 d-flex flex-wrap justify-content-between align-items-center gap-3">
              <h5 class="mb-0 fw-semibold text-success"><i class="fas fa-file me-2"></i>All uploads</h5>
              <span class="badge bg-success-subtle text-success fw-semibold px-3 py-2">Total: <?= count($uploads) ?></span>
            </div>
            <div class="card-body table-responsive">
              <table class="table table-hover align-middle text-nowrap mb-0">
                <thead class="table-light">
                  <tr>
                    <th>#</th>
                    <th>User</th>
                    <th>File</th>
                    <th>Date</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php $count = 1; foreach ($uploads as $file): ?>
                    <tr>
                      <td><?= $count++ ?></td>
                      <td class="text-start">
                        <span class="fw-semibold d-block"><?= htmlspecialchars($file['user_name']) ?></span>
                        <small class="text-muted"><?= htmlspecialchars($file['email']) ?></small>
                      </td>
                      <td class="text-start">
                        <a href="<?= htmlspecialchars($file['file_url']) ?>" target="_blank" class="link-primary"><?= htmlspecialchars($file['file_name']) ?></a>
                      </td>
                      <td><?= date('d M Y, h:i A', strtotime($file['created_at'])) ?></td>
                      <td>
                        <div class="d-inline-flex gap-2">
                          <a href="<?= htmlspecialchars($file['file_url']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-link"></i>
                          </a>
                          <a href="delete_upload.php?id=<?= $file['id'] ?>"
                             class="btn btn-sm btn-outline-danger"
                             data-confirm="Removing this file will also invalidate its QR code link."
                             data-confirm-title="Delete file?"
                             data-confirm-button="Delete"
                             data-confirm-icon="error">
                             <i class="fas fa-trash-alt"></i>
                          </a>
                        </div>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

    </div>
  </section>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
