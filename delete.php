<?php
session_start();
require 'db.php';

// must be logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$postId = (int) ($_GET['post_id'] ?? 0);
if ($postId <= 0) {
    http_response_code(400);
    exit('Invalid post id');
}

// find the tweet and confirm the caller owns it
$stmt = $pdo->prepare('SELECT userid FROM tweets WHERE id = ?');
$stmt->execute([$postId]);
$tweet = $stmt->fetch();

if (!$tweet) {
    http_response_code(404);
    exit('Tweet not found');
}

if ((int) $tweet['userid'] !== (int) $_SESSION['user_id']) {
    http_response_code(403);
    exit('You can only delete your own tweets');
}

// delete it
$stmt = $pdo->prepare('DELETE FROM tweets WHERE id = ?');
$stmt->execute([$postId]);

// return to the owner's public profile
header('Location: profile.php?userid=' . (int) $_SESSION['user_id']);
exit;