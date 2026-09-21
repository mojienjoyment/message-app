<?php
session_start();
require 'db.php';

$loggedIn = isset($_SESSION['user_id']);
$error = $success = '';
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
        <div id="tweetFeed">
          <p class="box">Loading tweets…</p>
        </div>
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

    /* ── load recent tweets via XHR — content never sits in the page source ── */
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'tweets.php', true);
    xhr.onreadystatechange = function () {
      if (xhr.readyState === XMLHttpRequest.DONE) {
        if (xhr.status === 200) {
          var data = JSON.parse(xhr.responseText);
          renderTweets(data);
        } else {
          document.getElementById('tweetFeed').innerHTML =
            '<p class="box err">Could not load tweets.</p>';
        }
      }
    };
    xhr.send();

    function renderTweets(tweets) {
      var feed = document.getElementById('tweetFeed');
      feed.innerHTML = '';

      if (!tweets.length) {
        var empty = document.createElement('p');
        empty.className = 'box';
        empty.textContent = 'No tweets yet. Be the first!';
        feed.appendChild(empty);
        return;
      }

      tweets.forEach(function (t) {
        var card = document.createElement('div');
        card.className = 'card tweet';

        var head = document.createElement('div');
        head.className = 'tweet-head';

        var link = document.createElement('a');
        link.className = 'tweet-user';
        link.href = 'profile.php?userid=' + encodeURIComponent(t.userid);

        var img = document.createElement('img');
        img.className = 'tweet-avatar';
        img.src = 'user_profiles/' + encodeURIComponent(t.profile_pic);
        img.alt = '';

        var name = document.createElement('span');
        name.className = 'tweet-name';
        name.textContent = t.name;              // textContent = XSS-safe

        var handle = document.createElement('span');
        handle.className = 'tweet-handle';
        handle.textContent = '@' + t.username;

        link.appendChild(img);
        link.appendChild(name);
        link.appendChild(handle);

        var time = document.createElement('small');
        time.textContent = t.created_at;

        head.appendChild(link);
        head.appendChild(time);

        var body = document.createElement('p');
        body.textContent = t.content;           // textContent = XSS-safe

        card.appendChild(head);
        card.appendChild(body);
        feed.appendChild(card);
      });
    }
  </script>

</body>

</html>