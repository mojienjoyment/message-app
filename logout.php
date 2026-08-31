<?php
session_start();
session_unset();  // clear session data
session_destroy();  // destroy the session
header('Location: login.php');
exit;
