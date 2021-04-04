<?php
//for logout
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    setcookie("sessionCookie", "", time() - 60 * 60 * 24, "/");
    unset($_SESSION);
    // unset session cookie
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
    session_destroy();

    header("Location: index.php#");
    exit;
}
