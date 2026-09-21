<?php
session_start();
require_once("db.php");
$tweets = [];
$stmt = $pdo->query(
    'SELECT t.content, t.created_at, t.userid, u.username, u.profile_pic, u.name
         FROM tweets t JOIN users u ON t.userid = u.id
         ORDER BY t.created_at DESC LIMIT 30'
);
$tweets = $stmt->fetchAll();
header('Content-Type: application/json');
echo json_encode($tweets);
?>