<?php
session_start();
require 'db.php';

// Already logged in?
if (isset($_SESSION['user_id'])) {
    header('Location: panel.php');
    exit;
}

$notice = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');

    if ($username === '') {
        $error = 'Please enter your username.';
    } else {
        // generate a 64-character hex token
        $rawToken = bin2hex(random_bytes(32));
        // hash it before storing (so a DB leak can't reveal valid tokens)
        $hashedToken = hash('sha256', $rawToken);
        $expiresAt = date('Y-m-d H:i:s', time() + 3600);  // 1 hour from now

        $stmt = $pdo->prepare(
            'UPDATE users
             SET reset_token = ?, token_expires_at = ?
             WHERE username = ?'
        );
        $stmt->execute([$hashedToken, $expiresAt, $username]);

        // $stmt->rowCount() tells us if the user existed
        // First, get the user's actual email
        $emailStmt = $pdo->prepare('SELECT email FROM users WHERE username = ?');
        $emailStmt->execute([$username]);
        $userData = $emailStmt->fetch();

        if ($userData) {
            // build the reset URL
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'];
            $resetUrl = "$protocol://$host/reset-password.php?token=" . urlencode($rawToken);

            // ✅ SEND TO THE ACTUAL EMAIL COLUMN
            $to = $userData['email'];
            $subject = 'Password reset';
            $message = "Hi,\n\nClick this link to reset your password (valid for 1 hour):\n$resetUrl\n\nIf you didn't request this, ignore this email.";
            $headers = 'From: no-reply@yourdomain.com';

            $logFile = __DIR__ . '/logs/sent_emails.log';
            file_put_contents($logFile, "To: $to\nSubject: $subject\n\n$message\n---\n", FILE_APPEND);
        }

        // ⚠️ Always show the same message — prevents user enumeration
        $notice = 'If that account exists, a reset link has been sent to your email.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Forgot password</title>
<style>
  body{font-family:system-ui,sans-serif;background:#f4f5f9;display:grid;place-items:center;min-height:100vh;margin:0}
  .card{background:#fff;padding:2.2rem;border-radius:10px;box-shadow:0 10px 30px rgba(0,0,0,.08);width:min(400px,92vw)}
  h1{margin:0 0 1.4rem;font-size:1.5rem}
  label{display:block;font-size:.85rem;font-weight:600;margin-bottom:1rem}
  input{display:block;width:100%;margin-top:.3rem;padding:.6rem .7rem;border:1px solid #c9cdd8;border-radius:6px;font-size:1rem;box-sizing:border-box}
  input:focus{outline:2px solid #4a6cf7;border-color:transparent}
  button{width:100%;padding:.8rem;border:0;border-radius:6px;background:#4a6cf7;color:#fff;font-size:1rem;font-weight:700;cursor:pointer}
  button:hover{background:#3a58d4}
  .box{padding:.8rem 1rem;border-radius:6px;font-size:.9rem;margin-bottom:1.2rem}
  .box.err{background:#fdeceb;color:#b3261e}
  .box.ok{background:#e6f6ea;color:#1c7c33}
  a{color:#4a6cf7}
</style>
</head>
<body>
<div class="card">
  <h1>Forgot password</h1>

  <?php if ($notice): ?><p class="box ok"><?= htmlspecialchars($notice) ?></p><?php endif; ?>
  <?php if ($error): ?><p class="box err"><?= htmlspecialchars($error) ?></p><?php endif; ?>

  <form method="post" action="forgot-password.php">
    <label>Username <input name="username" required autofocus></label>
    <button type="submit">Send reset link</button>
  </form>
  <p style="margin-top:1rem;font-size:.9rem;">Remembered it? <a href="login.php">Back to login</a></p>
</div>
</body>
</html>