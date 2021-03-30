<?php
// Initialization
// REQUIREMENTS
require_once "utility/database.class.php";
require_once "models/UserType.php";
require_once "models/Game.php";
require_once "models/User.php";
require_once "services/UserService.class.php";
require_once "services/GameService.class.php";

// GET/SET session
session_set_cookie_params(0, "/", $_SERVER['HTTP_HOST'], (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'on'), true);
session_start();

// GET LOGIN STATUS
$loggedIn = false;
/** @var UserType */
$userType = null;
if(isset($_SESSION['SessionID'])) {
    // Search for set session id and get user
    if(($user = UserService::$instance->getUserSession($_SESSION['SessionID'])) == null)
        return;
    
    $loggedIn = true;
    $userType = $user->getUserType();
}

// DEBUG USER ADMIN
echo "DEBUGGIN ENABLED - LOGGED IN AND ROLE VIA SESSION PARAMETER";
$loggedIn = true;
if(isset($_GET['debugRole'])) {
    $_SESSION['debugRole'] = $_GET['debugRole'];
}
if(isset($_SESSION['debugRole'])) {
    $userType = new UserType($_SESSION['debugRole']);
}
else {
    $userType = UserType::User();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ITP-Minigames</title>

    <!-- IMPORT BOOTSTRAP -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js" integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0" crossorigin="anonymous"></script>

    <!-- IMPORT CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://use.fontawesome.com/d95cfc3de4.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/userAdministration.css">
    <link rel="stylesheet" href="css/game.css">

    <!-- IMPORT JS -->
    
</head>
<body>
    <!-- Testing ground -->
    <div class="ps-3 mt-3 pt-2 border-top">
        <p class="h5">Components</p>
        <a href="http://localhost/?action=showUsers&amount=20&offset=0" class="btn btn-success">User List</a>
        <a href="http://localhost/?action=viewGame&id=1" class="btn btn-success">View Game</a>
    </div>
    <div class="ps-3 mt-2 mb-3 pb-3 border-bottom">
        <p class="h5">Roles</p>
        <a href="http://localhost/?debugRole=user" class="btn btn-success">User Role</a>
        <a href="http://localhost/?debugRole=creator" class="btn btn-success">Creator Role</a>
        <a href="http://localhost/?debugRole=admin" class="btn btn-success">Admin Role</a>
    </div>
    <!-- Main container -->
    <div class="container">
        <?php
            /// Load components
            // Check which sites can be seen
            // Load public components
            require_once "utility/GameRenderer.php";

            // Load logged in components
            if($loggedIn) {
                $accessStrength = $userType->getAccessStrength();
                // Normal user components
                if($accessStrength >= UserType::User()->getAccessStrength()) {

                }
                // Game creator components
                if($accessStrength >= UserType::Creator()->getAccessStrength()) {

                }
                // Admin components
                if($accessStrength >= UserType::Admin()->getAccessStrength()) {
                    require_once "utility/UserAdministration.php";
                }
                
            }
        ?>
    </div>
</body>
</html>