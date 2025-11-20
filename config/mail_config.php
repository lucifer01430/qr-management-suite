<?php
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

require_once __DIR__ . '/../vendor/autoload.php';

define('EMAIL_HOST', 'smtp.hostinger.com');
define('EMAIL_PORT', 465);
define('EMAIL_USE_SSL', true);
define('EMAIL_HOST_USER', 'testing@pixelperfectstrategies.com');
define('EMAIL_HOST_PASSWORD', 'vQ?dcr8f1');

function qr_portal_base_url(): string {
    if (!empty($_SERVER['HTTP_HOST'])) {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $scriptDir = isset($_SERVER['SCRIPT_NAME']) ? rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/') : '';
        $path = ($scriptDir && $scriptDir !== '/') ? $scriptDir : '';
        return rtrim($scheme . '://' . $_SERVER['HTTP_HOST'] . $path, '/') . '/';
    }

    return 'https://qrportal.example.com/';
}

function build_mail_template(string $title, string $bodyHtml, ?string $ctaLabel = null, ?string $ctaUrl = null, string $preheader = 'Secure QR automation for your documents.'): string {
    $titleEsc = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
    $preheaderEsc = htmlspecialchars($preheader, ENT_QUOTES, 'UTF-8');
    $year = date('Y');

    $ctaBlock = '';
    if ($ctaLabel && $ctaUrl) {
        $ctaLabelEsc = htmlspecialchars($ctaLabel, ENT_QUOTES, 'UTF-8');
        $ctaUrlEsc = htmlspecialchars($ctaUrl, ENT_QUOTES, 'UTF-8');
        $ctaBlock = "
            <tr>
                <td align=\"center\" style=\"padding: 0 32px 32px;\">
                    <a href=\"{$ctaUrlEsc}\" style=\"display:inline-block;padding:14px 32px;background:linear-gradient(135deg,#4f46e5,#3b82f6);color:#ffffff;text-decoration:none;border-radius:999px;font-weight:600;\">{$ctaLabelEsc}</a>
                </td>
            </tr>
        ";
    }

    return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>QR Portal</title>
</head>
<body style="margin:0;padding:0;background-color:#f4f6fb;font-family:'Inter',Arial,sans-serif;color:#0f172a;">
  <div style="display:none;max-height:0;overflow:hidden;">{$preheaderEsc}</div>
  <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color:#f4f6fb;padding:32px 16px;">
    <tr>
      <td align="center">
        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:600px;background-color:#ffffff;border-radius:22px;overflow:hidden;box-shadow:0 20px 60px rgba(15,23,42,0.12);">
          <tr>
            <td style="padding:32px;background:linear-gradient(135deg,#4f46e5,#3b82f6);">
              <h1 style="margin:0;font-size:22px;font-weight:700;color:#ffffff;">{$titleEsc}</h1>
              <p style="margin:8px 0 0;font-size:14px;color:rgba(255,255,255,0.75);">Stay on top of your documents with QR Portal.</p>
            </td>
          </tr>
          <tr>
            <td style="padding:32px;">
              {$bodyHtml}
            </td>
          </tr>
          {$ctaBlock}
          <tr>
            <td style="padding:0 32px 32px;">
              <p style="margin:0;font-size:13px;color:#64748b;">If you did not request this email, you can safely ignore it.</p>
              <p style="margin:12px 0 0;font-size:12px;color:#94a3b8;">&copy; {$year} QR Portal. Designed &amp; developed by Harsh Pandey.</p>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>
HTML;
}

function sendEmail(string $to, string $subject, string $htmlBody): bool {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = EMAIL_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = EMAIL_HOST_USER;
        $mail->Password = EMAIL_HOST_PASSWORD;
        $mail->SMTPSecure = EMAIL_USE_SSL ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = EMAIL_PORT;

        $mail->setFrom(EMAIL_HOST_USER, 'QR Portal');
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $htmlBody;

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log('Email Error: ' . $e->getMessage());
        return false;
    }
}

function send_otp_mail(string $toEmail, string $toName, string $otp): bool {
    $safeName = htmlspecialchars($toName, ENT_QUOTES, 'UTF-8');
    $otpValue = htmlspecialchars($otp, ENT_QUOTES, 'UTF-8');
    $portalUrl = qr_portal_base_url();

    $subject = 'Your QR Portal verification code';

    $bodyContent = "
        <p style=\"margin:0 0 16px;font-size:16px;color:#0f172a;\">Hi {$safeName},</p>
        <p style=\"margin:0 0 16px;font-size:15px;color:#334155;\">Use the One-Time Password below to verify your email address and continue with QR Portal.</p>
        <div style=\"margin:24px 0;padding:20px;border-radius:18px;background:rgba(79,70,229,0.12);text-align:center;font-size:32px;letter-spacing:10px;font-weight:700;color:#312e81;\">{$otpValue}</div>
        <p style=\"margin:0 0 16px;font-size:15px;color:#334155;\">This code expires in <strong>10 minutes</strong>. Enter it in the portal to finish the process.</p>
        <p style=\"margin:0;font-size:14px;color:#64748b;\">If you didn’t request this, you can safely ignore this email.</p>
    ";

    $emailHtml = build_mail_template(
        'Your QR Portal verification code',
        $bodyContent,
        'Open QR Portal',
        $portalUrl,
        'Your QR Portal verification code is ready.'
    );

    return sendEmail($toEmail, $subject, $emailHtml);
}

