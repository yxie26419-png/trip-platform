<?php
session_start();

// Destroy session
session_unset();
session_destroy();

// Clear remember me cookie
if (isset($_COOKIE['user_id'])) {
    setcookie('user_id', '', time() - 3600, '/');
}

// Redirect to login
header("Location: login.php");
exit;
?>