<?php
session_start();
require 'db.php';

// 🔒 Guard: no login session → send to login page
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Logged in → read user info from the session
$name = $_SESSION['name'];
$username = $_SESSION['username'];
$email = $_SESSION['email'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Panel</title>
<style>
  body{font-family:system-ui,sans-serif;background:#f4f5f9;margin:0}
  .topbar{background:#17224f;color:#fff;padding:1rem 2rem;display:flex;justify-content:space-between;align-items:center}
  .topbar a{color:#ffd24a;text-decoration:none}
  .wrap{max-width:720px;margin:2rem auto;padding:0 1rem}
  .card{background:#fff;padding:2rem;border-radius:10px;box-shadow:0 10px 30px rgba(0,0,0,.08)}
  h1{margin-top:0}
  table{border-collapse:collapse;width:100%}
  th,td{text-align:left;padding:.5rem .75rem;border-bottom:1px solid #eee}
  th{color:#666;font-weight:600;width:140px}
</style>
</head>
<body>
<div class="topbar">
  <strong>My Panel</strong>
  <a href="logout.php">Logout</a>
</div>
<div class="wrap">
  <div class="card">
    <h1>Welcome, <?= htmlspecialchars($name) ?> 👋</h1>
    <p>You are logged in. This is your panel page.</p>
    <table>
      <tr><th>Name</th><td><?= htmlspecialchars($name) ?></td></tr>
      <tr><th>Username</th><td><?= htmlspecialchars($username) ?></td></tr>
      <tr><th>Email</th><td><?= htmlspecialchars($email) ?></td></tr>
    </table>
  </div>
</div>
</body>
</html>