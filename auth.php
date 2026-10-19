<?php
/* Shared auth bootstrap:
   - starts the session
   - loads db.php (connection) + config.php ($hmac_key)
   - !session + valid remember-me cookie  →  grant a session
*/
session_start();
require_once 'db.php';
require_once 'config.php';

if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_me'])) {
    $parts = explode('|', $_COOKIE['remember_me'], 2);

    if (count($parts) === 2 && ctype_digit($parts[0])) {
        [$cookieUid, $cookieHmac] = $parts;
        $expectedHmac = hash_hmac('sha256', $cookieUid, $hmac_key);

        // timing-safe comparison — never use == for hashes
        if (hash_equals($expectedHmac, $cookieHmac)) {
            $stmt = $pdo->prepare('SELECT id, name, username, email FROM users WHERE id = ?');
            $stmt->execute([(int) $cookieUid]);
            $u = $stmt->fetch();

            if ($u) {
                session_regenerate_id(true);   // no session fixation
                $_SESSION['user_id'] = $u['id'];
                $_SESSION['name'] = $u['name'];
                $_SESSION['username'] = $u['username'];
                $_SESSION['email'] = $u['email'];
            }
        }
    }
}