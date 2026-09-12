<?php
/* ════════════════════════════════════════════════
   PART 1 — CONNECTION (same as save-name.php)
   ════════════════════════════════════════════════ */
require 'db.php';

try {
  $pdo = new PDO(
    "mysql:host=$host;dbname=$db;charset=utf8mb4",
    $user,
    $pass,
    [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]
  );
} catch (PDOException $e) {
  http_response_code(500);
  exit('Database connection failed');
}

/* ════════════════════════════════════════════════
   PART 2 — REGISTRATION LOGIC
   ════════════════════════════════════════════════ */
$errors = [];
$success = false;
$old = ['name' => '', 'username' => '', 'email' => '', 'code' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = trim($_POST['name'] ?? '');
  $username = trim($_POST['username'] ?? '');
  $email = strtolower(trim($_POST['email'] ?? ''));
  $pass1 = $_POST['password'] ?? '';
  $code = strtoupper(trim($_POST['code'] ?? ''));

  // keep values if the form is shown again (never the password!)
  $old = ['name' => $name, 'username' => $username, 'email' => $email, 'code' => $code];

  // ── validation ──
  if ($name === '' || mb_strlen($name) > 60)
    $errors[] = 'Name is required (max 60 characters).';
  if (!preg_match('/^[a-zA-Z0-9_]{3,30}$/', $username))
    $errors[] = 'Username must be 3–30 characters: letters, numbers, underscores.';
  if (!filter_var($email, FILTER_VALIDATE_EMAIL))
    $errors[] = 'Enter a valid email address.';
  if (strlen($pass1) < 8)
    $errors[] = 'Password must be at least 8 characters.';
  if ($code === '')
    $errors[] = 'An invitation code is required.';

  // ── username must be unique ──
  if (!$errors) {
    $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
    $stmt->execute([$username]);
    if ($stmt->fetch())
      $errors[] = 'This username is already taken.';
  }

  // ── email must be unique ──
  if (!$errors) {
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->execute([$email]);
    if ($stmt->fetch())
      $errors[] = 'This email is already registered.';
  }

  // ── invitation code must exist and never have been used ──
  if (!$errors) {
    $stmt = $pdo->prepare('SELECT id FROM invitation_codes WHERE invitation_code = ? AND used = 0');
    $stmt->execute([$code]);
    $invite = $stmt->fetch();
    if (!$invite)
      $errors[] = 'Invalid or already used invitation code.';
  }

  // ── everything OK → create the account ──
  if (!$errors) {
    $stmt = $pdo->prepare(
      'INSERT INTO users (name, username, email, password) VALUES (?, ?, ?, ?)'
    );
    $stmt->execute([
      $name,
      $username,
      $email,
      password_hash($pass1, PASSWORD_DEFAULT)
    ]);

    $pdo
      ->prepare('UPDATE invitation_codes SET used = 1 WHERE id = ?')
      ->execute([$invite['id']]);

    // ✅ after register → send user to the login page
    header('Location: login.php?registered=1');
    exit;
  }
}
?>

<!-- ════════════════════════════════════════════════
     PART 3 — THE FORM
     ════════════════════════════════════════════════ -->
<!DOCTYPE html>
<html lang="en">

<head>
  <link rel="stylesheet" href="styles.css">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Register</title>
</head>

<body class="page-auth">
  <div class="card">
    <h1>Create account</h1>

    <form method="post" action="register.php">
      <label>Name
        <input name="name" required maxlength="60" value="<?= htmlspecialchars($old['name']) ?>">
      </label>
      <label>Username
        <input name="username" required minlength="3" maxlength="30" pattern="[a-zA-Z0-9_]+"
          title="Letters, numbers and underscores only" value="<?= htmlspecialchars($old['username']) ?>"
          placeholder="e.g. jane_doe">
      </label>
      <label>Email
        <input type="email" name="email" required value="<?= htmlspecialchars($old['email']) ?>">
      </label>
      <label>Password
        <input type="password" name="password" required minlength="8" placeholder="At least 8 characters">
      </label>
      <label>Invitation code
        <input name="code" required value="<?= htmlspecialchars($old['code']) ?>" placeholder="e.g. ABC123">
      </label>
      <button type="submit">Register</button>
    </form>
  </div>
</body>

</html>