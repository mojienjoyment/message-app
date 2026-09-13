<?php
session_start();
require 'db.php';

// 🔒 Guard: no login session → go to login
if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}

$error = $success = '';

/* ════════════════════════════════════════════════
   PART 1 — HANDLE THE UPDATE (runs on form submit)
   ════════════════════════════════════════════════ */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = trim($_POST['name'] ?? '');
  $email = strtolower(trim($_POST['email'] ?? ''));
  $currentPass = $_POST['current_password'] ?? '';
  $newPass = $_POST['new_password'] ?? '';
  $confirmPass = $_POST['confirm_password'] ?? '';

  // fetch the current user from the DB
  $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
  $stmt->execute([$_SESSION['user_id']]);
  $user = $stmt->fetch();

  if (!$user) {
    session_destroy();
    header('Location: login.php');
    exit;
  }

  // verify current password before allowing any change
  if (!password_verify($currentPass, $user['password'])) {
    $error = 'Current password is incorrect.';
  } else {
    $errors = [];

    if ($name === '' || mb_strlen($name) > 60)
      $errors[] = 'Name is required (max 60 characters).';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL))
      $errors[] = 'Enter a valid email address.';

    // optional password change
    $changePassword = false;
    if ($newPass !== '') {
      $changePassword = true;
      if (strlen($newPass) < 8)
        $errors[] = 'New password must be at least 8 characters.';
      if ($newPass !== $confirmPass)
        $errors[] = 'New passwords do not match.';
    }

    if (!$errors) {
      if ($changePassword) {
        $stmt = $pdo->prepare(
          'UPDATE users SET name = ?, email = ?, password = ? WHERE id = ?'
        );
        $stmt->execute([$name, $email, password_hash($newPass, PASSWORD_DEFAULT), $_SESSION['user_id']]);
      } else {
        $stmt = $pdo->prepare(
          'UPDATE users SET name = ?, email = ? WHERE id = ?'
        );
        $stmt->execute([$name, $email, $_SESSION['user_id']]);
      }

      // keep the session in sync with the new values
      $_SESSION['name'] = $name;
      $_SESSION['email'] = $email;

      $success = 'Profile updated successfully.';
    } else {
      $error = implode(' ', $errors);
    }
  }
}

/* ════════════════════════════════════════════════
   PART 2 — FETCH FRESH DATA TO DISPLAY
   ════════════════════════════════════════════════ */
$stmt = $pdo->prepare('SELECT id, name, username, email, created_at FROM users WHERE id = ?');
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user) {
  session_destroy();
  header('Location: login.php');
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Panel</title>
  <link rel="stylesheet" href="styles.css">
  <style>
    /* small page-specific helpers */
    .grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 0 1rem;
    }

    input[readonly] {
      background: #f0f1f5;
      color: #888;
      cursor: not-allowed;
    }

    .divider {
      margin: 1.5rem 0 1rem;
      border: 0;
      border-top: 1px dashed #d0d4e0;
    }

    .hint {
      font-size: .8rem;
      color: #888;
      margin-top: -.6rem;
      margin-bottom: 1rem;
    }
  </style>
</head>

<body class="page-panel">

  <div class="topbar">
    <strong>My Panel</strong>
    <a href="logout.php">Logout</a>
  </div>

  <div class="wrap">
    <div class="card">
      <h1>Edit Profile</h1>

        <?php if ($success): ?>
        <p class="box ok">✅ <?= htmlspecialchars($success) ?></p><?php endif; ?>
        <?php if ($error): ?>
        <p class="box err"><?= htmlspecialchars($error) ?></p><?php endif; ?>

      <form method="post" action="panel.php">

        <div class="grid">
          <label>Name
            <input name="name" required maxlength="60" value="<?= htmlspecialchars($user['name']) ?>">
          </label>
          <label>Username
            <input value="<?= htmlspecialchars($user['username']) ?>" readonly>
          </label>
        </div>

        <label>Email
          <input type="email" name="email" required value="<?= htmlspecialchars($user['email']) ?>">
        </label>

        <label>Member since
          <input value="<?= htmlspecialchars($user['created_at']) ?>" readonly>
        </label>

        <hr class="divider">

        <label>Current password <span style="color:#b3261e">*</span>
          <input type="password" name="current_password" required placeholder="Required to save changes">
        </label>

        <div class="grid">
          <label>New password
            <input type="password" name="new_password" minlength="8" placeholder="Leave blank to keep current">
          </label>
          <label>Confirm new password
            <input type="password" name="confirm_password" minlength="8">
          </label>
        </div>
        <p class="hint">Fill in "New password" only if you want to change it.</p>

        <button type="submit">Save changes</button>
      </form>
    </div>
  </div>

</body>

</html>