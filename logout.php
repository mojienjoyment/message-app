<?php
session_start();
session_unset();  // clear session data
session_destroy();  // destroy the session
session_regenerate_id(true);
header('Location: login.php');
exit;
