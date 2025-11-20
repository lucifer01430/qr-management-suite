<?php
require_once __DIR__ . '/config/db.php';

$message = '';
if (empty($_SESSION['pending_email'])) {
    header("Location: register.php");
    exit;
}

$email = $_SESSION['pending_email'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otp_input = trim($_POST['otp']);
    $pdo = db();
    $stmt = $pdo->prepare("SELECT id, otp FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && $user['otp'] == $otp_input) {
        $update = $pdo->prepare("UPDATE users SET verified = 1, otp = NULL WHERE id = ?");
        $update->execute([$user['id']]);
        unset($_SESSION['pending_email']);
        $_SESSION['message'] = "Account verified successfully! Please login.";
        header("Location: login.php");
        exit;
    } else {
        $message = "Invalid OTP. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verify OTP - QR Portal</title>
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
    <div class="d-none" data-flash-message="<?= htmlspecialchars($message, ENT_QUOTES) ?>" data-flash-type="error" data-flash-title="Verification failed"></div>
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
                                <i class="fas fa-user-check"></i>
                                Verify account
                            </span>
                            <h1 class="auth-card__title mt-4 mb-3">Almost there</h1>
                            <p class="text-white-50 mb-4">Enter the verification code we emailed to <strong><?= htmlspecialchars($email) ?></strong>.</p>
                        </div>
                        <div class="d-flex flex-column gap-3 mt-auto">
                            <div class="d-flex gap-3 align-items-center">
                                <span class="icon-circle bg-white text-primary shadow-sm">
                                    <i class="fas fa-envelope-open-text"></i>
                                </span>
                                <p class="text-white-50 small mb-0">If you can't find the email, check your spam folder or request a new code.</p>
                            </div>
                            <div class="d-flex gap-3 align-items-center">
                                <span class="icon-circle bg-white text-primary shadow-sm">
                                    <i class="fas fa-clock"></i>
                                </span>
                                <p class="text-white-50 small mb-0">Codes expire for security—complete verification within 10 minutes.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-7 auth-card__content">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div>
                                <h2 class="h3 mb-1">Enter verification code</h2>
                                <p class="auth-card__subtitle mb-0">We use this code to validate your email address</p>
                            </div>
                            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill d-flex align-items-center gap-2" data-theme-toggle>
                                <i class="fas fa-moon"></i>
                                <span data-theme-label class="small">Dark mode</span>
                            </button>
                        </div>
                        <form method="POST" class="auth-form" novalidate>
                            <div class="mb-4">
                                <label for="otpCode" class="form-label">Verification code</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-shield-alt"></i></span>
                                    <input type="text" id="otpCode" name="otp" class="form-control text-center" maxlength="6" placeholder="000000" required>
                                </div>
                                <div class="form-text">Didn't get the code? Click below to return and resend.</div>
                            </div>
                            <div class="d-grid gap-3">
                                <button type="submit" class="btn btn-primary fw-semibold">
                                    <i class="fas fa-check-circle me-2"></i>Verify account
                                </button>
                                <a href="register.php" class="btn btn-outline-primary">
                                    <i class="fas fa-arrow-left me-2"></i>
                                    Back to sign up
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
