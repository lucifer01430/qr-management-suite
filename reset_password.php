<?php
require_once __DIR__ . '/config/db.php';

if (empty($_SESSION['reset_email'])) {
    header("Location: forgot_password.php");
    exit;
}

$email = $_SESSION['reset_email'];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otp = trim($_POST['otp']);
    $newpass = trim($_POST['new_password']);
    $pdo = db();
    $stmt = $pdo->prepare("SELECT id, otp FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && $user['otp'] == $otp) {
        $hash = password_hash($newpass, PASSWORD_BCRYPT);
        $update = $pdo->prepare("UPDATE users SET password = ?, otp = NULL WHERE id = ?");
        $update->execute([$hash, $user['id']]);
        unset($_SESSION['reset_email']);
        $_SESSION['message'] = "Password reset successful. Please login.";
        header("Location: login.php");
        exit;
    } else {
        $message = "Invalid OTP. Try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Reset Password - QR Portal</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="icon" type="image/png" href="assets/favicon/favicon-96x96.png" sizes="96x96" />
<link rel="icon" type="image/svg+xml" href="assets/favicon/favicon.svg" />
<link rel="shortcut icon" href="assets/favicon/favicon.ico" />
<link rel="apple-touch-icon" sizes="180x180" href="assets/favicon/apple-touch-icon.png" />
<meta name="apple-mobile-web-app-title" content="Qr-Portal" />
<link rel="manifest" href="assets/favicon/site.webmanifest" />
</head>
<?php
$flash = $_SESSION['message'] ?? '';
$flashType = $_SESSION['message_type'] ?? 'success';
if ($flash) {
    unset($_SESSION['message'], $_SESSION['message_type']);
}
?>
<body class="auth-wrapper">
<?php if ($message): ?>
  <div class="d-none" data-flash-message="<?= htmlspecialchars($message, ENT_QUOTES) ?>" data-flash-type="error" data-flash-title="Reset failed"></div>
<?php endif; ?>
<?php if ($flash): ?>
  <div class="d-none" data-flash-message="<?= htmlspecialchars($flash, ENT_QUOTES) ?>" data-flash-type="<?= htmlspecialchars($flashType, ENT_QUOTES) ?>" data-flash-title="All set!"></div>
<?php endif; ?>
<div class="container">
  <div class="row justify-content-center">
    <div class="col-xxl-6 col-xl-7">
      <div class="auth-card">
        <div class="row g-0 align-items-stretch">
          <div class="col-lg-5 auth-card__hero">
            <div>
              <span class="auth-card__badge text-uppercase fw-semibold">
                <i class="fas fa-user-shield"></i>
                Secure reset
              </span>
              <h1 class="auth-card__title mt-4 mb-3">Set a new password</h1>
              <p class="text-white-50 mb-4">Enter the OTP sent to <strong><?= htmlspecialchars($email) ?></strong> and choose a fresh, secure password.</p>
            </div>
            <div class="d-flex flex-column gap-3 mt-auto">
              <div class="d-flex gap-3 align-items-center">
                <span class="icon-circle bg-white text-primary shadow-sm">
                  <i class="fas fa-shield-alt"></i>
                </span>
                <p class="text-white-50 small mb-0">We never store passwords in plain text—only encrypted hashes.</p>
              </div>
              <div class="d-flex gap-3 align-items-center">
                <span class="icon-circle bg-white text-primary shadow-sm">
                  <i class="fas fa-clock"></i>
                </span>
                <p class="text-white-50 small mb-0">OTPs expire quickly—request a new one if this code stops working.</p>
              </div>
            </div>
          </div>
          <div class="col-lg-7 auth-card__content">
            <div class="d-flex justify-content-between align-items-start mb-4">
              <div>
                <h2 class="h3 mb-1">Reset password</h2>
                <p class="auth-card__subtitle mb-0">Verify the code and choose a strong password</p>
              </div>
              <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill d-flex align-items-center gap-2" data-theme-toggle>
                <i class="fas fa-moon"></i>
                <span data-theme-label class="small">Dark mode</span>
              </button>
            </div>
            <form method="POST" class="auth-form" novalidate>
              <div class="mb-4">
                <label for="resetOtp" class="form-label">One-time passcode</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="fas fa-key"></i></span>
                  <input type="text" id="resetOtp" name="otp" class="form-control text-center" maxlength="6" placeholder="000000" required>
                </div>
                <div class="form-text">Check your email for the 6-digit code.</div>
              </div>
              <div class="mb-4">
                <label for="resetPassword" class="form-label">New password</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="fas fa-lock"></i></span>
                  <input type="password" id="resetPassword" name="new_password" class="form-control" placeholder="Choose a secure password" required>
                  <button class="btn btn-light border-0 px-3 text-muted" type="button" data-password-toggle="#resetPassword" aria-label="Toggle password visibility">
                    <i class="fas fa-eye"></i>
                  </button>
                </div>
                <div class="form-text">Use a combination of upper/lowercase letters, numbers, and symbols.</div>
              </div>
              <div class="d-grid gap-3">
                <button type="submit" class="btn btn-primary">
                  <i class="fas fa-rotate me-2"></i>
                  <span class="fw-semibold">Update password</span>
                </button>
                <a href="forgot_password.php" class="btn btn-outline-primary">
                  <i class="fas fa-arrow-left me-2"></i>
                  Back to recovery
                </a>
              </div>
              <div class="auth-credit text-center mt-3">
                Designed &amp; developed by
                <a href="https://lucifer01430.github.io/Portfolio/" target="_blank" rel="noopener">Harsh Pandey</a>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
<script src="assets/js/main.js"></script>
</body>
</html>
