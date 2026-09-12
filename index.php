<?php
session_start();
$loggedIn = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <link rel="stylesheet" href="styles.css">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Home</title>
</head>

<body class="page-index">
  <header class="topbar">
    <a class="brand" href="index.php">MySite</a>
    <nav>
      <a href="login.php">Login</a>
      <a href="register.php">Register</a>
      <a href="panel.php">Panel</a>
      <?php if ($loggedIn): ?><a href="logout.php">Logout</a><?php endif; ?>
    </nav>
  </header>

  <main class="hero">
    <?php if ($loggedIn): ?>
      <h1>Welcome back, <?= htmlspecialchars($_SESSION['name']) ?> 👋</h1>
      <p>You are logged in. Head to your panel to see your account.</p>
      <a class="btn btn-primary" href="panel.php">Go to Panel</a>
    <?php else: ?>
      <h1>Welcome 👋</h1>
      <p>This site is invitation-only. Have an invitation code?
        Create your account in a minute, then log in to access your panel.</p>
      <a class="btn btn-primary" href="register.php">Register</a>
      <a class="btn btn-ghost" href="login.php">Login</a>
    <?php endif; ?>
  </main>

  <footer>© <?= date('Y') ?> MySite — invitation required</footer>

</body>

</html>