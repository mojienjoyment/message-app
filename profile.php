<?php
require 'db.php';

$userId = (int) ($_GET['userid'] ?? 0);
if ($userId <= 0) {
    http_response_code(400);
    exit('Invalid user id');
}

// 1) user info (name, username, bio, pic) from the internal Flask API
$apiUrl = "http://127.0.0.1:5000/api/user/$userId";
$apiJson = @file_get_contents($apiUrl);
$user = $apiJson ? json_decode($apiJson, true) : null;

if (!$user || isset($user['error'])) {
    http_response_code(404);
    exit('User not found (is internal.py running?)');
}

// 2) tweets straight from MySQL
$stmt = $pdo->prepare(
    'SELECT content, created_at FROM tweets WHERE userid = ? ORDER BY created_at DESC LIMIT 50'
);
$stmt->execute([$userId]);
$tweets = $stmt->fetchAll();

$picUrl = 'user_profiles/' . htmlspecialchars($user['profile_pic'] ?? 'default.png');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@<?= htmlspecialchars($user['username']) ?> — Public profile</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body class="page-profile">

    <div class="topbar">
        <a class="brand" href="index.php">MySite</a>
        <nav><a href="index.php">Home</a></nav>
    </div>

    <div class="wrap">
        <div class="card">
            <div class="profile-head">
                <img src="<?= $picUrl ?>" alt="profile picture" class="avatar">
                <div>
                    <h1><?= htmlspecialchars($user['name']) ?></h1>
                    <p class="username">@<?= htmlspecialchars($user['username']) ?></p>
                    <p class="bio"><?= htmlspecialchars($user['bio']) ?></p>
                </div>
            </div>
        </div>

        <h2 class="tweets-title">Tweets</h2>
        <?php if (!$tweets): ?>
            <p class="box">No tweets yet.</p>
        <?php else: ?>
            <?php foreach ($tweets as $t): ?>
                <div class="card tweet">
                    <p><?= htmlspecialchars($t['content']) ?></p>
                    <small><?= htmlspecialchars($t['created_at']) ?></small>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</body>

</html>