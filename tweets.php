<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json');

/* ════════════════════════════════════════════════
   GET — return recent tweets as JSON
   ════════════════════════════════════════════════ */
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $all_tweets = [];
    $sql = "SELECT t.id, t.userid, t.content, t.created_at, u.name, u.username, u.profile_pic
            FROM tweets t INNER JOIN users u ON t.userid = u.id
            ORDER BY t.created_at DESC LIMIT 30";
    $stmt = $pdo->query($sql);

    while ($row = $stmt->fetch()) {
        // human-readable date, like the reference code
        if (!empty($row['created_at'])) {
            $dt = new DateTime($row['created_at']);
            $row['created_human'] = $dt->format('M j, Y \a\t H:i');
        } else {
            $row['created_human'] = '';
        }

        // picture URL, like the reference code
        $picture = $row['profile_pic'] ?? 'default.png';
        // if you ever get a static/CDN domain, use this instead:
        // $row['profile_picture_url'] = 'https://static.yourdomain.ir/user_profiles/' . rawurlencode($picture);
        $row['profile_picture_url'] = 'user_profiles/' . rawurlencode($picture);

        $all_tweets[] = $row;
    }

    echo json_encode($all_tweets);
    exit;
}

/* ════════════════════════════════════════════════
   POST — insert a new tweet (JSON body)
   ════════════════════════════════════════════════ */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['error' => 'You must be logged in to submit a tweet']);
        exit;
    }

    $data = json_decode(file_get_contents('php://input'), true);
    $tweet_content = trim($data['tweet_content'] ?? '');

    if (empty($tweet_content)) {
        echo json_encode(['error' => 'Tweet content is required']);
        exit;
    }
    if (mb_strlen($tweet_content) > 280) {
        echo json_encode(['error' => 'Tweet must be 280 characters or less']);
        exit;
    }

    $stmt = $pdo->prepare('INSERT INTO tweets (userid, content) VALUES (?, ?)');
    if ($stmt->execute([$_SESSION['user_id'], $tweet_content])) {
        echo json_encode(['success' => 'Tweet submitted successfully']);
    } else {
        echo json_encode(['error' => 'Failed to submit tweet']);
    }
    exit;
}

// anything else (PUT, DELETE, ...)
http_response_code(405);
echo json_encode(['error' => 'Method not allowed']);