<?php

if (isset($_POST['RegisterSubmit'])) {
    $_SESSION['register'] = $_POST; // store post data in session to keep data after refresh (sending form) 
    // validate input
    if (Validation::$instance->register($_POST)) {
        // clear possible previous error messages
        if (isset($_SESSION['registerErrors']))
            unset($_SESSION['registerErrors']);

        // insert into database...
        $_SESSION['UserID'] =  UserService::$instance->insertUserData($_POST);;
        header("Location: index.php");
        exit;
    } else {
        // get errors and store into session variable
        $_SESSION['registerErrors'] = Validation::$instance->getReturnErrors();
    }
}

if (isset($_POST['LoginSubmit'])) {
    if (Validation::$instance->login($_POST)) {
        if (isset($_SESSION['loginErrors']))
            unset($_SESSION['loginErrors']);

        $user =  UserService::$instance->getUserByUsername($_POST['Username']);

        if (isset($_POST['rememberme'])) {
            $sessionID = session_id() . time();
            setcookie("sessionCookie", $sessionID, time() + 60 * 60 * 24, "/");
            UserService::$instance->updateSessionID($sessionID, $user->getId());
        }

        $_SESSION['UserID'] = $user->getId();
        header("Location: index.php");
        exit;
    } else {
        $_SESSION['loginErrors'] = Validation::$instance->getReturnErrors();
    }
}


