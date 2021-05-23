<?php
/// Initialization
// Set Session
session_set_cookie_params(0, "/", $_SERVER['HTTP_HOST'], (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'on'), true);
session_start();

// REQUIREMENTS
require_once "utility/database.class.php";
require_once "utility/Initialization.php";

require_once "models/Platform.php";
require_once "models/UserType.php";
require_once "models/Game.php";
require_once "models/User.php";
require_once "models/Picture.php";
require_once "models/Post.php";
require_once "models/Rating.php";

require_once "utility/PictureUpload.class.php";
require_once "utility/Validation.class.php";

require_once "services/UserService.class.php";
require_once "services/ProfilePictureService.class.php";
require_once "services/FavoriteService.class.php";
require_once "services/GameService.class.php";
require_once "services/FrontPageService.class.php";
require_once "services/ForumService.class.php";
require_once "services/RatingService.class.php";

require_once "utility/favoritelogic.php";

//BOOL FOR DEBUGGIG MODE
$showDebug = false;


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
if ($showDebug) {
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

            //print current user 
            if (isset($user)) {
                echo "<br>" . $user->getUsername();
            }
        }
    }
    // Print debugging status
    echo "DEBUGGING ENABLED<br>LOGGED IN: <b>" . ($loggedIn ? "YES" : "NO") . "</b><br>ROLE: <b>" . (($userType != null) ? $userType->getTypeString() : "none") . "</b>";
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

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/userAdministration.css">
    <link rel="stylesheet" type="text/css" href="css/crop.css" />
    <link rel="stylesheet" type="text/css" href="css/game.css">
    <link rel="stylesheet" type="text/css" href="css/gameUploadInterface.css"/>
    <link rel="stylesheet" type="text/css" href="css/forum.css">
    <link rel="stylesheet" type="text/css" href="css/rating.css">

    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.0/font/bootstrap-icons.css">
 
    <!-- JQUERY -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <!-- IMPORT JS -->
    <script src="scripts/ts/compiled/Crop.js"></script>
    <script src="scripts/js/rater.js"></script>

</head>

<body>
  
    <nav class="navbar navbar-dark bg-dark">

        <a href="index.php" class="btn btn-success">View Front Page</a>
        <a href="index.php?action=forum" class="btn btn-success">Forum</a>
        <form action="index.php" method="GET">
            <input type="search" name="search" placeholder="search...">
        </form>
        <?php
        if ($loggedIn) {
            $accessStrength = $userType->getAccessStrength();
            $_SESSION['AccessStrength'] = $accessStrength;
            // Normal user components
            if ($accessStrength >= UserType::User()->getAccessStrength()) {
        ?>
                <a href="index.php?action=editProfile" class="btn btn-success">Edit Profile</a>
                <a href="index.php?action=logout" class="btn btn-success">Logout</a>
            <?php
            }
            // Game creator components
            if ($accessStrength >= UserType::Creator()->getAccessStrength()) {
            ?>
                <a href="index.php?action=listCreatedGames" class="btn btn-success">Created Games List</a>
            <?php

            }
            // Admin components
            if ($accessStrength >= UserType::Admin()->getAccessStrength()) {
            ?> 
            <a href="index.php?action=showUsers&amount=20&offset=0" class="btn btn-success">User List</a>
            <a href="index.php?action=listGamesToVerify&amount=20&offset=0" class="btn btn-success">Game Verification List</a>
            <?php
            }
        } else {
            // if someone isn´t logged in
            ?>
            <a href="index.php?action=register" class="btn btn-success">Registration</a>
            <a href="index.php?action=login" class="btn btn-success">Login</a>
        <?php
        }
        ?>
    </nav>

    <?php
    if ($showDebug) {
    ?> <div class="ps-3 mt-3 pt-2 border-top">

            <p class="h5">Components</p>

            <a href="index.php?action=showUsers&amount=20&offset=0" class="btn btn-success">User List</a>
            <a href="index.php?action=viewGame&id=1" class="btn btn-success">View Game</a>
            <a href="index.php?action=viewFrontPage" class="btn btn-success">View Front Page</a>
            <a href="index.php?action=forum" class="btn btn-success">Forum</a>
            <a href="index.php?action=listCreatedGames" class="btn btn-success">Created Games List</a>
            <a href="index.php?action=register" class="btn btn-success">Registration</a>
            <a href="index.php?action=editProfile" class="btn btn-success">Edit Profile</a>
            <a href="index.php?action=login" class="btn btn-success">Login</a>
            <a href="index.php?action=logout" class="btn btn-success">Logout</a>

        </div>
        <div class="ps-3 mt-2 mb-3 pb-3 border-bottom">
            <p class="h5">Roles</p>
            <a href="index.php?debugLogin=<?= !$loggedIn ?>" class="btn btn-success">Toggle Login</a>
            <a href="index.php?debugRole=user" class="btn btn-success">User Role</a>
            <a href="index.php?debugRole=creator" class="btn btn-success">Creator Role</a>
            <a href="index.php?debugRole=admin" class="btn btn-success">Admin Role</a>
        </div>
    <?php
    } ?>


    <!-- Main container -->
    <div class="container">
        <?php
        require_once "forms/formHandler.php";

        /// Load components
        // Check which sites can be seen
        // Load public components
        require_once "utility/GameRenderer.php";

        require_once "utility/frontPage.php";

        //make this for users only, this is public for debugg only
        require_once "utility/ForumMainPage.php";


        // Load logged in components
        if ($loggedIn) {
            $accessStrength = $userType->getAccessStrength();
            // Normal user components
            if ($accessStrength >= UserType::User()->getAccessStrength()) {
                require_once "utility/EditProfile.php";
            }
            // Game creator components
            if ($accessStrength >= UserType::Creator()->getAccessStrength()) {
                require_once "utility/GameUploadInterface.php";
                require_once "utility/GameList.php";
            }
            // Admin components
            if ($accessStrength >= UserType::Admin()->getAccessStrength()) {
                require_once "utility/UserAdministration.php";
                require_once "utility/GameVerificationList.php";
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