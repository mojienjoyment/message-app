<?php
session_start();

// 1) wipe every session variable
$_SESSION = [];

// 2) delete the session cookie from the browser
//    (must reuse the same path/domain/secure/httponly params,
//     otherwise the browser sees it as a different cookie and keeps the old one)
if (ini_get('session.use_cookies')) {
    $p = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $p['path'],
        $p['domain'],
        $p['secure'],
        $p['httponly']
    );
}

// 3) destroy the session data on the server — the old ID is now dead
session_destroy();

// 4) OPTIONAL: hand the browser a brand-new EMPTY session with a fresh ID.
//    Uncomment only if you need a session after logout (e.g. flash messages).
//    Leave commented if you want the cookie fully gone:
// session_start();
// session_regenerate_id(true);

header('Location: login.php');
exit;