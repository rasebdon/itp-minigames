<?php
// Initialization
// REQUIREMENTS
require "utility/database.class.php";
require "models/Game.php";
require "models/User.php";
require "services/UserService.class.php";
require "services/GameService.class.php";
require "utility/GameRenderer.php";
require "utility/UserAdministration.php";

// GET/SET session
session_set_cookie_params(0, "/", $_SERVER['HTTP_HOST'], (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'on'), true);
session_start();

// GET LOGIN STATUS
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
    <link rel="stylesheet" href="css/userAdministration.css">
    <link rel="stylesheet" href="css/game.css">

    <!-- IMPORT JS -->
    
</head>
<body>
    <div class="container">
        <?php 
            UserAdministration::ShowUsers($_GET['position'], $_GET['loaded'], $_GET['amount']);
        ?>
    </div>
</body>
</html>