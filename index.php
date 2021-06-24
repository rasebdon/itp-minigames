<?php
/// Initialization
// Set Session
session_set_cookie_params(0, "/", $_SERVER['HTTP_HOST'], (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'on'), true);
session_start();

// REQUIREMENTS
require_once "services/Database.service.php";
require_once "services/Initialization.service.php";

// Load Models
require_once "models/Platform.model.php";
require_once "models/UserType.model.php";
require_once "models/Game.model.php";
require_once "models/User.model.php";
require_once "models/Picture.model.php";
require_once "models/Post.model.php";
require_once "models/Comment.model.php";
require_once "models/Rating.model.php";

require_once "utility/Validation.class.php";

// Load Services
require_once "services/User.service.php";
require_once "services/ProfilePicture.service.php";
require_once "services/PictureUpload.service.php";
require_once "services/Favorite.service.php";
require_once "services/Game.service.php";
require_once "services/Rating.service.php";
require_once "services/Forum.service.php";
require_once "services/Contact.service.php";

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

require_once "forms/formHandler.php";

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
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.3/css/all.css">
    <script src="https://use.fontawesome.com/d95cfc3de4.js"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/userAdministration.css">
    <link rel="stylesheet" type="text/css" href="css/game.css">
    <link rel="stylesheet" type="text/css" href="css/gameUploadInterface.css" />
    <link rel="stylesheet" type="text/css" href="css/forum.css">
    <link rel="stylesheet" type="text/css" href="css/rating.css">
    <link rel="stylesheet" type="text/css" href="css/impressum.css">
    <link rel="stylesheet" type="text/css" href="css/styleComp.css">
    <link rel="stylesheet" type="text/css" href="css/contact.css">


    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.0/font/bootstrap-icons.css">

    <!-- JQUERY -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <!-- IMPORT JS -->
    <script src="scripts/ts/compiled/Crop.js"></script>
    <script src="scripts/js/rater.js"></script>

</head>

<body>
    <?php
<<<<<<< Updated upstream
    // Load navbar
    require_once "components/Navigation.component.php";
=======
    require_once "utility/navigation.php";

    if ($showDebug) {
>>>>>>> Stashed changes
    ?>

    <!-- Main container -->
    <div class="container">
        <?php

        /// Load components
        // Check which sites can be seen
        // Load public components
        require_once "components/GameRenderer.component.php";
        require_once "components/FrontPage.component.php";
        require_once "components/Imprint.component.php";
        require_once "components/Contact.component.php";
        require_once "components/ForumMainPage.component.php";
        require_once "components/PostRenderer.component.php";

        // Load logged in components
        if ($loggedIn) {
            $accessStrength = $userType->getAccessStrength();
            // Normal user components
            if ($accessStrength >= UserType::User()->getAccessStrength()) {
                require_once "components/EditProfile.component.php";
            }
            // Game creator components
            if ($accessStrength >= UserType::Creator()->getAccessStrength()) {
                require_once "components/GameUpload.component.php";
                require_once "components/GameEdit.component.php";
                require_once "components/GameList.component.php";
            }
            // Admin components
            if ($accessStrength >= UserType::Admin()->getAccessStrength()) {
                require_once "components/UserAdministration.component.php";
                require_once "components/GameVerification.component.php";
            }
        } else {
            // if someone isn't logged in
            require_once "components/Registration.component.php";
            require_once "components/Login.component.php";
        }
        ?>
    </div>
</body>

</html>