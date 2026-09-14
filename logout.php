<?php
/**
 * User Logout Handler
 * 
 * Safely unsets all session variables, invalidates the session cookie,
 * terminates the session via session_destroy(), and redirects to login.php.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Unset all session superglobal values
$_SESSION = array();

// If session cookie is used, clear it from browser
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Destroy session data on server
session_destroy();

// Redirect back to login page with feedback flag
header("Location: login.php?logged_out=1");
exit();
