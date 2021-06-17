<?php
if ($loggedIn) {
    
    $accessStrength = $userType->getAccessStrength();
    // Normal user components
    if ($accessStrength >= UserType::User()->getAccessStrength()) {
        require_once "forms/formUserUpdate.php";
        require_once "forms/formGame.php";
    }
    // Game creator components
    if ($accessStrength >= UserType::Creator()->getAccessStrength()) {
    }
    // Admin components
    if ($accessStrength >= UserType::Admin()->getAccessStrength()) {
    }
} else {
    // if someone isn´t logged in
    require_once "forms/formEntry.php";
}
