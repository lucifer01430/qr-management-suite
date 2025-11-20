<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/mail_config.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);
    $pass  = trim($_POST['password']);

    if ($name && $email && $pass) {
        $pdo = db();
        // check if email exists
        $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $check->execute([$email]);

        if ($check->rowCount() > 0) {
            $message = "Email already registered!";
        } else {
            $otp = rand(100000, 999999);
            $hash = password_hash($pass, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("INSERT INTO users (name,email,password,otp,verified) VALUES (?,?,?,?,0)");
            $stmt->execute([$name, $email, $hash, $otp]);

            if (send_otp_mail($email, $name, $otp)) {
                $_SESSION['pending_email'] = $email;
                header("Location: verify_otp.php");
                exit;
            } else {
                $message = "Failed to send OTP email.";
            }
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
    <title>Register - QR Portal</title>
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
    <div class="d-none" data-flash-message="<?= htmlspecialchars($message, ENT_QUOTES) ?>" data-flash-type="error" data-flash-title="Sign up issue"></div>
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
                            <h1 class="auth-card__title mt-4 mb-3">Create your workspace</h1>
                            <p class="text-white-50 mb-4">Bring your team together to generate, manage, and track QR codes from anywhere.</p>
                        </div>
                        <div class="d-flex flex-column gap-3 mt-auto">
                            <div class="d-flex gap-3 align-items-center">
                                <span class="icon-circle bg-white text-primary shadow-sm">
                                    <i class="fas fa-layer-group"></i>
                                </span>
                                <p class="text-white-50 small mb-0">Collaborate with role-based access and shared libraries.</p>
                            </div>
                            <div class="d-flex gap-3 align-items-center">
                                <span class="icon-circle bg-white text-primary shadow-sm">
                                    <i class="fas fa-bolt"></i>
                                </span>
                                <p class="text-white-50 small mb-0">Generate QR codes in seconds with automated styling presets.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-7 auth-card__content">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div>
                                <h2 class="h3 mb-1">Sign up</h2>
                                <p class="auth-card__subtitle mb-0">Create an account in a few quick steps</p>
                            </div>
                            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill d-flex align-items-center gap-2" data-theme-toggle>
                                <i class="fas fa-moon"></i>
                                <span data-theme-label class="small">Dark mode</span>
                            </button>
                        </div>
                        <form method="POST" class="auth-form" novalidate>
                            <div class="mb-4">
                                <label for="registerName" class="form-label">Full name</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="text" id="registerName" name="name" class="form-control" placeholder="Jane Doe" required>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label for="registerEmail" class="form-label">Email address</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" id="registerEmail" name="email" class="form-control" placeholder="name@example.com" required>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label for="registerPassword" class="form-label">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" id="registerPassword" name="password" class="form-control" placeholder="Create a secure password" required>
                                    <button class="btn btn-light border-0 px-3 text-muted" type="button" data-password-toggle="#registerPassword" aria-label="Toggle password visibility">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div class="form-text">Use at least 8 characters with a mix of letters and numbers.</div>
                            </div>
                            <div class="d-grid gap-3">
                                <button type="submit" class="btn btn-primary">
                                    <span class="fw-semibold">Create account</span>
                                </button>
                                <a href="login.php" class="btn btn-outline-primary">I already have an account</a>
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
