<?php
// 1. Initialize or resume the session
session_start();

// 2. Unset all session variables
$_SESSION = array();

// 3. Delete the session cookie from the browser if it exists
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), 
        '', 
        time() - 42000, // Set expiration time to the past to delete it
        $params["path"], 
        $params["domain"], 
        $params["secure"], 
        $params["httponly"]
    );
}

// 4. Destroy the session on the server
session_destroy();

// 5. Redirect the user back to the login page
header("Location: Login.php");
exit;
?>
