<?php
require 'db.php';

$stmt = $pdo->query(
    'SELECT id, name, username, bio, profile_pic 
     FROM users 
     ORDER BY created_at DESC'
);
$users = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>All Users</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .users-grid {
            max-width: 900px;
            margin: 2rem auto;
            padding: 0 1rem;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1rem;
        }

        .user-card {
            background: #fff;
            padding: 1.2rem;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, .06);
            text-decoration: none;
            color: inherit;
            transition: transform .2s, box-shadow .2s;
        }

        .user-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, .1);
        }

        .user-card .avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: .8rem;
        }

        .user-card h3 {
            margin: 0 0 .2rem;
            font-size: 1.1rem;
        }

        .user-card .username {
            color: #4a6cf7;
            font-weight: 600;
            margin: 0 0 .5rem;
            font-size: .9rem;
        }

        .user-card .bio {
            color: #666;
            font-size: .85rem;
            margin: 0;
            line-height: 1.4;
        }
    </style>
</head>

<body class="page-users">

    <div class="topbar">
        <a class="brand" href="index.php">MySite</a>
        <nav>
            <a href="index.php">Home</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="panel.php">Panel</a>
            <?php endif; ?>
        </nav>
    </div>

    <div class="wrap">
        <h1 style="text-align:center;margin:1.5rem 0;">All Users</h1>
    </div>

    <div class="users-grid">
        <?php if (!$users): ?>
            <p class="box" style="grid-column:1/-1;text-align:center;">No users yet.</p>
        <?php else: ?>
            <?php foreach ($users as $u): ?>
                <a href="profile.php?userid=<?= (int) $u['id'] ?>" class="user-card">
                    <img src="user_profiles/<?= htmlspecialchars($u['profile_pic']) ?>" alt="profile picture" class="avatar">
                    <h3><?= htmlspecialchars($u['name']) ?></h3>
                    <p class="username">@<?= htmlspecialchars($u['username']) ?></p>
                    <p class="bio"><?= htmlspecialchars($u['bio']) ?></p>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</body>

</html>