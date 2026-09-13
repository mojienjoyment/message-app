<?php
session_start();
require 'db.php';

$loggedIn = isset($_SESSION['user_id']);
$error = $success = '';
$tweets = [];

/* ════════════════════════════════════════════════
   HANDLE TWEET SUBMISSION
   ════════════════════════════════════════════════ */
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

/* ════════════════════════════════════════════════
   FETCH RECENT TWEETS (for logged-in users)
   ════════════════════════════════════════════════ */
if ($loggedIn) {
  $stmt = $pdo->query(
    'SELECT t.content, t.created_at, u.username 
         FROM tweets t 
         JOIN users u ON t.userid = u.id 
         ORDER BY t.created_at DESC 
         LIMIT 20'
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
  <style>
    .tweet-form {
      max-width: 620px;
      margin: 2rem auto 1rem;
      padding: 0 1rem;
    }

    .tweet-form .card {
      padding: 1.5rem;
    }

    .tweet-form textarea {
      min-height: 80px;
      margin-bottom: 1rem;
    }

    .char-count {
      float: right;
      font-size: .8rem;
      color: #999;
    }

    .char-count.warn {
      color: #b3261e;
    }

    .tweet-feed {
      max-width: 620px;
      margin: 0 auto;
      padding: 0 1rem;
    }

    .tweet-feed h2 {
      margin: 1.5rem 0 1rem;
      font-size: 1.2rem;
    }
  </style>
</head>

<body class="page-index">

  <header class="topbar">
    <a class="brand" href="index.php">MySite</a>
    <nav>
        <?php if ($loggedIn): ?>
        <a href="panel.php">Panel</a>
        <a href="all_users.php">Users</a>
        <a href="logout.php">Logout</a>
        <?php else: ?>
        <a href="login.php">Login</a>
        <a href="register.php">Register</a>
        <a href="all_users.php">Users</a>
        <?php endif; ?>
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

    <?php if ($loggedIn): ?>
    <!-- Tweet Form -->
    <div class="tweet-form">
      <div class="card">
          <?php if ($success): ?>
          <p class="box ok">✅ <?= htmlspecialchars($success) ?></p><?php endif; ?>
          <?php if ($error): ?>
          <p class="box err"><?= htmlspecialchars($error) ?></p><?php endif; ?>

        <form method="post" action="index.php" id="tweetForm">
          <label>What's happening?
            <textarea name="tweet" maxlength="280" rows="3" placeholder="Write your tweet…" oninput="updateCharCount()"
              required></textarea>
          </label>
          <span class="char-count" id="charCount">0 / 280</span>
          <button type="submit">Tweet</button>
        </form>
      </div>
    </div>

    <!-- Recent Tweets -->
    <div class="tweet-feed">
      <h2>Recent tweets</h2>
        <?php if (!$tweets): ?>
        <p class="box">No tweets yet. Be the first!</p>
        <?php else: ?>
          <?php foreach ($tweets as $t): ?>
          <div class="card tweet">
            <p><strong>@<?= htmlspecialchars($t['username']) ?>:</strong> <?= htmlspecialchars($t['content']) ?></p>
            <small><?= htmlspecialchars($t['created_at']) ?></small>
          </div>
          <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <?php endif; ?>

  <footer>© <?= date('Y') ?> MySite — invitation required</footer>

  <script>
    function updateCharCount() {
      const textarea = document.querySelector('textarea[name="tweet"]');
      const counter = document.getElementById('charCount');
      const len = textarea.value.length;
      counter.textContent = len + ' / 280';
      counter.classList.toggle('warn', len > 260);
    }
  </script>

</body>

</html>