<?php
session_start();
require 'db.php';

$loggedIn = isset($_SESSION['user_id']);
$error = $success = '';
$tweets = [];
$me = null;
$tweetCount = 0;

/* ── handle tweet submission ── */
if ($loggedIn && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tweet'])) {
  $content = trim($_POST['tweet'] ?? '');
  if ($content === '') {
    $error = 'Tweet cannot be empty.';
  } elseif (mb_strlen($content) > 280) {
    $error = 'Tweet must be 280 characters or less.';
  } else {
    $stmt = $pdo->prepare('INSERT INTO tweets (userid, content) VALUES (?, ?)');
    $stmt->execute([$_SESSION['user_id'], $content]);
    $success = 'Tweet posted!';
  }
}

/* ── data for logged-in view ── */
if ($loggedIn) {
  $stmt = $pdo->prepare('SELECT name, username, bio, profile_pic FROM users WHERE id = ?');
  $stmt->execute([$_SESSION['user_id']]);
  $me = $stmt->fetch();

  $stmt = $pdo->prepare('SELECT COUNT(*) FROM tweets WHERE userid = ?');
  $stmt->execute([$_SESSION['user_id']]);
  $tweetCount = (int) $stmt->fetchColumn();

  $stmt = $pdo->query(
    'SELECT t.content, t.created_at, t.userid, u.username
         FROM tweets t JOIN users u ON t.userid = u.id
         ORDER BY t.created_at DESC LIMIT 30'
  );
  $tweets = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Home</title>
  <link rel="stylesheet" href="styles.css">
</head>

<body class="page-index">

  <header class="topbar">
    <a class="brand" href="index.php">MySite</a>
    <nav>
      <a href="all_users.php">Users</a>
        <?php if ($loggedIn): ?>
        <a href="panel.php">Panel</a>
        <a href="logout.php">Logout</a>
        <?php else: ?>
        <a href="login.php">Login</a>
        <a href="register.php">Register</a>
        <?php endif; ?>
    </nav>
  </header>

    <?php if (!$loggedIn): ?>

    <main class="hero">
      <h1>Welcome 👋</h1>
      <p>This site is invitation-only. Have an invitation code?
        Create your account in a minute, then log in to join the feed.</p>
      <a class="btn btn-primary" href="register.php">Register</a>
      <a class="btn btn-ghost" href="login.php">Login</a>
    </main>

    <?php else: ?>

    <main class="layout">

      <!-- left: composer + feed -->
      <section>
        <div class="card composer">
            <?php if ($success): ?>
            <p class="box ok">✅ <?= htmlspecialchars($success) ?></p><?php endif; ?>
            <?php if ($error): ?>
            <p class="box err"><?= htmlspecialchars($error) ?></p><?php endif; ?>
          <form method="post" action="index.php">
            <textarea name="tweet" maxlength="280" rows="3"
              placeholder="What's happening, <?= htmlspecialchars($me['username']) ?>?" oninput="updateCharCount(this)"
              required></textarea>
            <div class="composer-row">
              <span class="char-count" id="charCount">0 / 280</span>
              <button type="submit">Tweet</button>
            </div>
          </form>
        </div>

        <h2 class="feed-title">Recent tweets</h2>
          <?php if (!$tweets): ?>
          <p class="box">No tweets yet. Be the first!</p>
          <?php else: ?>
            <?php foreach ($tweets as $t): ?>
            <div class="card tweet">
              <div class="tweet-head">
                <a href="profile.php?userid=<?= (int) $t['userid'] ?>">@<?= htmlspecialchars($t['username']) ?></a>
                <small><?= htmlspecialchars($t['created_at']) ?></small>
              </div>
              <p><?= htmlspecialchars($t['content']) ?></p>
            </div>
            <?php endforeach; ?>
          <?php endif; ?>
      </section>

      <!-- right: your profile card -->
      <aside>
        <div class="card side-card">
          <img class="avatar" src="user_profiles/<?= htmlspecialchars($me['profile_pic']) ?>" alt="your profile picture">
          <h3><?= htmlspecialchars($me['name']) ?></h3>
          <p class="username">@<?= htmlspecialchars($me['username']) ?></p>
          <p class="bio"><?= htmlspecialchars($me['bio']) ?></p>
          <p class="stat"><strong><?= $tweetCount ?></strong> tweets</p>
          <a class="btn btn-primary" href="panel.php">Panel</a>
          <a class="btn btn-ghost" target="_blank" href="profile.php?userid=<?= (int) $_SESSION['user_id'] ?>">Public
            profile</a>
        </div>
      </aside>

    </main>

    <?php endif; ?>

  <footer>© <?= date('Y') ?> MySite — invitation required</footer>

  <script>
    function updateCharCount(el) {
      const c = document.getElementById('charCount');
      c.textContent = el.value.length + ' / 280';
      c.classList.toggle('warn', el.value.length > 260);
    }
  </script>

</body>

</html>