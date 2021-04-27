<?php
// Initialization
// REQUIREMENTS
require_once "utility/database.class.php";
require_once "models/UserType.php";
require_once "models/Game.php";
require_once "models/User.php";
require_once "services/UserService.class.php";
require_once "services/FavoriteService.class.php";
require_once "services/GameService.class.php";
require_once "utility/Validation.class.php";
require_once "utility/favoritelogic.php";

// GET/SET session
session_set_cookie_params(0, "/", $_SERVER['HTTP_HOST'], (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'on'), true);
session_start();


// GET LOGIN STATUS
$loggedIn = false;
/** @var UserType */
$userType = null;

//check if cookie is set
if (isset($_COOKIE['sessionCookie'])) {
    $user = UserService::$instance->getUserSession($_COOKIE['sessionCookie']);

    //if user doesn´t exist
    if ($user == null)
        return;

    $loggedIn = true;
    $userType = $user->getUserType();
    $_SESSION['UserID'] = $user->getId();
} else if (isset($_SESSION['UserID'])) {
    // Search for set user id and get user
    if (($user = UserService::$instance->getUser($_SESSION['UserID'])) == null)
        return;

    $loggedIn = true;
    $userType = $user->getUserType();
}

/// DEBUGGING
// Debug login

if (isset($_GET['debugLogin'])) {
    $_SESSION['debugLogin'] = $_GET['debugLogin'];
}
if (isset($_SESSION['debugLogin'])) {
    $loggedIn = $_SESSION['debugLogin'];
    // Debug role (Only available if user is logged in)
    if ($loggedIn) {
        if (isset($_GET['debugRole'])) {
            $_SESSION['debugRole'] = $_GET['debugRole'];
        }
        if (isset($_SESSION['debugRole'])) {
            $userType = new UserType($_SESSION['debugRole']);
        } else {
            $userType = new UserType("user");
        }
    }
}
// Print debugging status
echo "DEBUGGING ENABLED<br>LOGGED IN: <b>" . ($loggedIn ? "YES" : "NO") . "</b><br>ROLE: <b>" . (($userType != null) ? $userType->getTypeString() : "none") . "</b>";

//print current user 
if (isset($user)) {
    echo "<br>" . $user->getUsername();
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
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://use.fontawesome.com/d95cfc3de4.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.0/font/bootstrap-icons.css">
    
    <link rel="stylesheet" type="text/css" href="css/userAdministration.css"/>
    <link rel="stylesheet" type="text/css" href="css/game.css"/>

    <!-- IMPORT JS -->

</head>

<body>
    <!-- Testing ground -->
    <div class="ps-3 mt-3 pt-2 border-top">
        <p class="h5">Components</p>
        <a href="index.php?action=showUsers&amount=20&offset=0" class="btn btn-success">User List</a>
        <a href="index.php?action=viewGame&id=1" class="btn btn-success">View Game</a>
        <a href="index.php?action=listCreatedGames" class="btn btn-success">Created Games List</a>
        <a href="index.php?action=register" class="btn btn-success">Registration</a>
        <a href="index.php?action=login" class="btn btn-success">Login</a>
        <a href="index.php?action=logout" class="btn btn-success">Logout</a>
    </div>
    <div class="ps-3 mt-2 mb-3 pb-3 border-bottom">
        <p class="h5">Roles</p>
        <a href="index.php?debugLogin=<?=!$loggedIn?>" class="btn btn-success">Toggle Login</a>
        <a href="index.php?debugRole=user" class="btn btn-success">User Role</a>
        <a href="index.php?debugRole=creator" class="btn btn-success">Creator Role</a>
        <a href="index.php?debugRole=admin" class="btn btn-success">Admin Role</a>
    </div>
    <!-- Main container -->
    <div class="container">
        <?php
        require_once "forms/formHandler.php";

        /// Load components
        // Check which sites can be seen
        // Load public components
        require_once "utility/GameRenderer.php";

        // Load logged in components
        if ($loggedIn) {
            $accessStrength = $userType->getAccessStrength();
            // Normal user components
            if ($accessStrength >= UserType::User()->getAccessStrength()) {
            }
            // Game creator components
            if ($accessStrength >= UserType::Creator()->getAccessStrength()) {
                require_once "utility/GameList.php";
            }
            // Admin components
            if ($accessStrength >= UserType::Admin()->getAccessStrength()) {
                require_once "utility/UserAdministration.php";
            }
        } else {
            // if someone isn´t logged in
            require_once "utility/Registration.php";
            require_once "utility/Login.php";
        }
        ?>
    </div>
</body>

</html>