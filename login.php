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
  <link rel="stylesheet" href="styles.css">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login</title>
  <?php if ($success): ?>
    <!-- ✅ redirect to panel.php after 3 seconds -->
    <!-- <meta http-equiv="refresh" content="3;url=panel.php"> -->
    <script>
      setTimeout(function () {
        location.href = 'panel.php';
      }, 3000);
    </script>
  <?php endif; ?>
</head>

<body class="page-auth">
  <div class="card">
    <h1>Login</h1>

    <?php if ($success): ?>
      <p class="box ok">✅ Login successfully! Redirecting to panel in 3 seconds…</p>
      <p>If not redirected, <a href="panel.php">click here</a>.</p>
    <?php else: ?>

      <?php if ($notice): ?>
        <p class="box ok"><?= htmlspecialchars($notice) ?></p><?php endif; ?>
      <?php if ($error): ?>
        <p class="box err"><?= htmlspecialchars($error) ?></p><?php endif; ?>

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