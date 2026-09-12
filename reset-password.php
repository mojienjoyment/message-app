<?php
session_start();
require 'db.php';

// Already logged in?
if (isset($_SESSION['user_id'])) {
    header('Location: panel.php');
    exit;
}

$error = $success = '';
$token = $_GET['token'] ?? ($_POST['token'] ?? '');
$valid = false;

if ($token !== '') {
    // hash the incoming token, same way we stored it
    $hashed = hash('sha256', $token);

    $stmt = $pdo->prepare(
        'SELECT id, username FROM users
         WHERE reset_token = ? AND token_expires_at > NOW()'
    );
    $stmt->execute([$hashed]);
    $user = $stmt->fetch();

    if ($user) {
        $valid = true;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $pass1 = $_POST['password'] ?? '';
            $pass2 = $_POST['confirm'] ?? '';

            if (strlen($pass1) < 8) {
                $error = 'Password must be at least 8 characters.';
            } elseif ($pass1 !== $pass2) {
                $error = 'Passwords do not match.';
            } else {
                $stmt = $pdo->prepare(
                    'UPDATE users
                     SET password = ?, reset_token = NULL, token_expires_at = NULL
                     WHERE id = ?'
                );
                $stmt->execute([
                    password_hash($pass1, PASSWORD_DEFAULT),
                    $user['id']
                ]);
                $success = true;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Reset password</title>
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
  <h1>Reset password</h1>

  <?php if ($success): ?>
    <p class="box ok">✅ Password updated. <a href="login.php">Login now</a></p>
  <?php elseif (!$valid): ?>
    <p class="box err">This reset link is invalid or has expired. <a href="forgot-password.php">Request a new one</a></p>
  <?php else: ?>
    <?php if ($error): ?><p class="box err"><?= htmlspecialchars($error) ?></p><?php endif; ?>
    <form method="post" action="reset-password.php">
      <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
      <label>New password <input type="password" name="password" required minlength="8"></label>
      <label>Confirm password <input type="password" name="confirm" required minlength="8"></label>
      <button type="submit">Change password</button>
    </form>
  <?php endif; ?>
</div>
</body>
</html>