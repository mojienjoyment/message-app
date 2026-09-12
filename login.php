<?php
session_start();
require 'db.php';

// Already logged in? Go straight to the panel
if (isset($_SESSION['user_id'])) {
  header('Location: panel.php');
  exit;
}

$error = $notice = '';
$success = false;

if (isset($_GET['registered'])) {
  $notice = 'Registration successful! Please log in.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username'] ?? '');
  $password = $_POST['password'] ?? '';
  $success = false;  // default to false for logging

  if ($username === '' || $password === '') {
    $error = 'Please enter both username and password.';
  } else {
    $stmt = $pdo->prepare(
      'SELECT id, name, username, email, password FROM users WHERE username = ?'
    );
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
      // ✅ add user information to the session
      $_SESSION['user_id'] = $user['id'];
      $_SESSION['name'] = $user['name'];
      $_SESSION['username'] = $user['username'];
      $_SESSION['email'] = $user['email'];

      $success = true;
    } else {
      $error = 'Invalid username or password.';
    }
  }

  // ── LOG THE ATTEMPT (Success or Fail) ──────────────────
  $ip = $_SERVER['REMOTE_ADDR'] ?? '';
  $ua = $_SERVER['HTTP_USER_AGENT'] ?? null;
  $referrer = $_SERVER['HTTP_REFERER'] ?? null;

  $stmt = $pdo->prepare(
    'INSERT INTO login_logs (username, ip, user_agent, referrer, login_status)
         VALUES (?, ?, ?, ?, ?)'
  );
  $stmt->execute([
    $username,
    $ip,
    $ua,
    $referrer,
    $success ? 200 : 401  // 200 for success, 401 for failure
  ]);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Login</title>
<?php if ($success): ?>
  <!-- ✅ redirect to panel.php after 3 seconds -->
  <meta http-equiv="refresh" content="3;url=panel.php">
<?php endif; ?>
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
  <h1>Login</h1>

  <?php if ($success): ?>
    <p class="box ok">✅ Login successfully! Redirecting to panel in 3 seconds…</p>
    <p>If not redirected, <a href="panel.php">click here</a>.</p>
  <?php else: ?>

    <?php if ($notice): ?><p class="box ok"><?= htmlspecialchars($notice) ?></p><?php endif; ?>
    <?php if ($error): ?><p class="box err"><?= htmlspecialchars($error) ?></p><?php endif; ?>

    <form method="post" action="login.php">
      <label>Username <input name="username" required></label>
      <label>Password <input type="password" name="password" required></label>
      <button type="submit">Login</button>
    </form>
    <p style="margin-top:1rem;font-size:.9rem;">No account? <a href="register.php">Register</a></p>
    <p style="margin-top:.5rem;font-size:.9rem;"><a href="forgot-password.php">Forgot password?</a></p>

  <?php endif; ?>
</div>
</body>
</html>