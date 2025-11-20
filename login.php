<?php
require_once __DIR__ . '/config/db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if ($email && $password) {
        $pdo = db();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && $user['verified'] == 1 && password_verify($password, $user['password'])) {
            // login success
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['message'] = "Welcome back, {$user['name']}!";
            $_SESSION['message_type'] = 'success';
            $_SESSION['message_title'] = 'Welcome to your dashboard';
            header("Location: dashboard.php");
            exit;
        } elseif ($user && $user['verified'] == 0) {
            $message = "Your account is not verified. Please check your email.";
        } else {
            $message = "Invalid email or password.";
        }
    } else {
        $message = "All fields are required!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login - QR Portal</title>
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
    <div class="d-none" data-flash-message="<?= htmlspecialchars($message, ENT_QUOTES) ?>" data-flash-type="error" data-flash-title="Login failed"></div>
  <?php endif; ?>
  <?php if ($flash): ?>
    <div class="d-none" data-flash-message="<?= htmlspecialchars($flash, ENT_QUOTES) ?>" data-flash-type="<?= htmlspecialchars($flashType, ENT_QUOTES) ?>" data-flash-title="All set!"></div>
  <?php endif; ?>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-xxl-7 col-xl-8">
        <div class="auth-card">
          <div class="row g-0 align-items-stretch">
            <div class="col-lg-5 auth-card__hero">
              <div>
                <span class="auth-card__badge text-uppercase fw-semibold">
                  <i class="fas fa-qrcode"></i>
                  QR Portal
                </span>
                <h1 class="auth-card__title mt-4 mb-3">Welcome back</h1>
                <p class="text-white-50 mb-4">Sign in to manage secure QR links, monitor engagement, and keep your documents organized.</p>
              </div>
              <div class="d-flex flex-column gap-3 mt-auto">
                <div class="d-flex gap-3 align-items-center">
                  <span class="icon-circle bg-white text-primary shadow-sm">
                    <i class="fas fa-shield-alt"></i>
                  </span>
                  <p class="text-white-50 small mb-0">Enterprise-grade encryption keeps your document links safe.</p>
                </div>
                <div class="d-flex gap-3 align-items-center">
                  <span class="icon-circle bg-white text-primary shadow-sm">
                    <i class="fas fa-chart-line"></i>
                  </span>
                  <p class="text-white-50 small mb-0">Track usage history and performance from a single dashboard.</p>
                </div>
              </div>
            </div>
            <div class="col-lg-7 auth-card__content">
              <div class="d-flex justify-content-between align-items-start mb-4">
                <div>
                  <h2 class="h3 mb-1">Sign in</h2>
                  <p class="auth-card__subtitle mb-0">Use your credentials to access the portal</p>
                </div>
                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill d-flex align-items-center gap-2" data-theme-toggle>
                  <i class="fas fa-moon"></i>
                  <span data-theme-label class="small">Dark mode</span>
                </button>
              </div>
              <form method="POST" class="auth-form" novalidate>
                <div class="mb-4">
                  <label for="loginEmail" class="form-label">Email address</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    <input type="email" id="loginEmail" name="email" class="form-control" placeholder="name@example.com" required>
                  </div>
                </div>
                <div class="mb-4">
                  <div class="d-flex justify-content-between align-items-center">
                    <label for="loginPassword" class="form-label mb-0">Password</label>
                    <a href="forgot_password.php" class="small text-decoration-none fw-semibold">Forgot password?</a>
                  </div>
                  <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" id="loginPassword" name="password" class="form-control" placeholder="Enter password" required>
                    <button class="btn btn-light border-0 px-3 text-muted" type="button" data-password-toggle="#loginPassword" aria-label="Toggle password visibility">
                      <i class="fas fa-eye"></i>
                    </button>
                  </div>
                </div>
                <div class="d-grid gap-3">
                  <button type="submit" class="btn btn-primary">
                    <span class="fw-semibold">Sign in</span>
                  </button>
                  <a href="register.php" class="btn btn-outline-primary">Create a QR Portal account</a>
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
