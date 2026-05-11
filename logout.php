<?php
/**
 * NHIF LOGOUT HANDLER
 * Purpose: Securely terminates the admin session and clears all authentication data.
 * Security Standard: OWASP A07:2021 - Identification and Authentication Failures.
 */

session_start();

// 1. Unset all session variables to clear the user's state
$_SESSION = array();

/**
 * 2. Delete the session cookie from the user's browser.
 * This ensures the session cannot be reused or hijacked.
 */
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 3. Completely destroy the session on the server side
session_destroy();

/**
 * 4. Redirect the administrator back to the login page.
 * We include a 'msg' parameter to trigger a logout notification on the UI.
 */
header("Location: auth_login.php?msg=logged_out");
exit();
?>