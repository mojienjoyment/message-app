<?php
session_start();
$loggedIn = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Home</title>
<style>
  * { box-sizing: border-box; margin: 0; }
  body { font-family: system-ui, sans-serif; background: #f4f5f9; color: #222; min-height: 100vh; display: flex; flex-direction: column; }

  /* top bar — same style as panel.php */
  .topbar { background: #17224f; color: #fff; display: flex; justify-content: space-between; align-items: center; padding: .9rem 1.5rem; }
  .brand { color: #fff; font-weight: 700; text-decoration: none; }
  .topbar nav a { color: #ffd24a; text-decoration: none; margin-left: 1.2rem; font-size: .95rem; }
  .topbar nav a:hover { text-decoration: underline; }

  /* hero */
  .hero { max-width: 620px; margin: auto; padding: 3rem 1rem; text-align: center; }
  .hero h1 { font-size: 2rem; margin-bottom: .6rem; }
  .hero p { color: #555; line-height: 1.6; margin-bottom: 1.8rem; }

  .btn { display: inline-block; padding: .7rem 1.5rem; border-radius: 6px; text-decoration: none; font-weight: 600; margin: 0 .3rem; }
  .btn-primary { background: #4a6cf7; color: #fff; }
  .btn-primary:hover { background: #3a58d4; }
  .btn-ghost { background: #fff; color: #333; border: 1px solid #c9cdd8; }
  .btn-ghost:hover { background: #eef1f8; }

  footer { text-align: center; color: #999; font-size: .8rem; padding: 1rem; }
</style>
</head>
<body>

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