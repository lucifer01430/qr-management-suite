<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/mail_config.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    if ($email) {
        $pdo = db();
        $stmt = $pdo->prepare("SELECT id, name FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            $otp = rand(100000, 999999);
            $update = $pdo->prepare("UPDATE users SET otp = ? WHERE id = ?");
            $update->execute([$otp, $user['id']]);
            
            if (send_otp_mail($email, $user['name'], $otp)) {
                $_SESSION['reset_email'] = $email;
                header("Location: reset_password.php");
                exit;
            } else {
                $message = "Failed to send reset OTP.";
            }
        } else {
            $message = "No account found with that email.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Forgot Password - QR Portal</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
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
  <div class="d-none" data-flash-message="<?= htmlspecialchars($message, ENT_QUOTES) ?>" data-flash-type="error" data-flash-title="Request failed"></div>
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
                <i class="fas fa-life-ring"></i>
                Account recovery
              </span>
              <h1 class="auth-card__title mt-4 mb-3">Reset access</h1>
              <p class="text-white-50 mb-4">We'll send a secure one-time password to confirm it's really you.</p>
            </div>
            <div class="d-flex flex-column gap-3 mt-auto">
              <div class="d-flex gap-3 align-items-center">
                <span class="icon-circle bg-white text-primary shadow-sm">
                  <i class="fas fa-lock-open"></i>
                </span>
                <p class="text-white-50 small mb-0">Quick, self-service recovery without contacting support.</p>
              </div>
              <div class="d-flex gap-3 align-items-center">
                <span class="icon-circle bg-white text-primary shadow-sm">
                  <i class="fas fa-envelope"></i>
                </span>
                <p class="text-white-50 small mb-0">Emails arrive within seconds—check your inbox and spam folder.</p>
              </div>
            </div>
          </div>
          <div class="col-lg-7 auth-card__content">
            <div class="d-flex justify-content-between align-items-start mb-4">
              <div>
                <h2 class="h3 mb-1">Send reset code</h2>
                <p class="auth-card__subtitle mb-0">Enter the email associated with your account</p>
              </div>
              <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill d-flex align-items-center gap-2" data-theme-toggle>
                <i class="fas fa-moon"></i>
                <span data-theme-label class="small">Dark mode</span>
              </button>
            </div>
            <form method="POST" class="auth-form" novalidate>
              <div class="mb-4">
                <label for="forgotEmail" class="form-label">Email address</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="fas fa-envelope-open-text"></i></span>
                  <input type="email" id="forgotEmail" name="email" class="form-control" placeholder="name@example.com" required>
                </div>
              </div>
              <div class="d-grid gap-3">
                <button type="submit" class="btn btn-primary">
                  <i class="fas fa-paper-plane me-2"></i>
                  <span class="fw-semibold">Send OTP</span>
                </button>
                <a href="login.php" class="btn btn-outline-primary">
                  <i class="fas fa-arrow-left me-2"></i>
                  Back to sign in
                </a>
              </div>
            </form>
            <div class="auth-credit text-center mt-3">
              Designed &amp; developed by
              <a href="https://lucifer01430.github.io/Portfolio/" target="_blank" rel="noopener">Harsh Pandey</a>
            </div>
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
