<?php
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/mail_config.php';

if ($_SESSION['role'] !== 'admin') {
  die("Access denied.");
}

if (!isset($_GET['id'])) {
  die("Invalid request.");
}

$pdo = db();
$id = intval($_GET['id']);

// 🔹 Step 1: Get file + user info before deleting
$stmt = $pdo->prepare("
  SELECT u.file_name, u.file_path, us.email, us.name
  FROM uploads u
  JOIN users us ON u.user_id = us.id
  WHERE u.id = ?
");
$stmt->execute([$id]);
$fileData = $stmt->fetch();

if ($fileData) {
  $file_path = __DIR__ . '/' . $fileData['file_path'];

  // 🔹 Step 2: Delete file from server
  if (file_exists($file_path)) {
    unlink($file_path);
  }

  // 🔹 Step 3: Delete record from DB
  $delete = $pdo->prepare("DELETE FROM uploads WHERE id = ?");
  $delete->execute([$id]);

  // 🔹 Step 4: Send email notification to user
  $to = $fileData['email'];
  $subject = "File Removed from QR Portal";

  $message = "
  <div style='font-family: Arial, sans-serif; background-color:#f6f6f6; padding:20px;'>
    <div style='max-width:600px; margin:auto; background:white; border-radius:8px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,0.1);'>
      <div style='background:#007bff; color:white; padding:15px; text-align:center;'>
        <h2 style='margin:0;'>QR Portal Notification</h2>
      </div>
      <div style='padding:20px;'>
        <h3 style='color:#333;'>Hello {$fileData['name']},</h3>
        <p style='font-size:15px; color:#555; line-height:1.6;'>
          We wanted to inform you that your uploaded file 
          <strong style='color:#007bff;'>{$fileData['file_name']}</strong> 
          has been <strong>removed by the admin</strong> from the QR Portal system.
        </p>
        <p style='font-size:15px; color:#555; line-height:1.6;'>
          If you believe this action was a mistake, please contact your administrator or support team.
        </p>
        <div style='margin:20px 0;'>
          <a href='https://your-subdomain.pixelperfectstrategies.com' 
             style='background:#007bff; color:white; text-decoration:none; padding:10px 20px; border-radius:5px;'>
            Go to QR Portal
          </a>
        </div>
        <hr style='border:none; border-top:1px solid #ddd;'>
        <p style='font-size:13px; color:#999; text-align:center;'>
          &copy; " . date('Y') . " QR Portal | Powered by Harsh Pandey
        </p>
      </div>
    </div>
  </div>
  ";

  sendEmail($to, $subject, $message);
}

header("Location: admin_dashboard.php?deleted=1");
exit;
