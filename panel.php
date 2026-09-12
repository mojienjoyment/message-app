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
  <link rel="stylesheet" href="styles.css">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Panel</title>
</head>

<body class="page-panel">
  <div class="topbar">
    <strong>My Panel</strong>
    <a href="logout.php">Logout</a>
  </div>
  <div class="wrap">
    <div class="card">
      <h1>Welcome, <?= htmlspecialchars($name) ?> 👋</h1>
      <p>You are logged in. This is your panel page.</p>
      <table>
        <tr>
          <th>Name</th>
          <td><?= htmlspecialchars($name) ?></td>
        </tr>
        <tr>
          <th>Username</th>
          <td><?= htmlspecialchars($username) ?></td>
        </tr>
        <tr>
          <th>Email</th>
          <td><?= htmlspecialchars($email) ?></td>
        </tr>
      </table>
    </div>
  </div>
</body>

</html>