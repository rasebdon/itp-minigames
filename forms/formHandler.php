<?php
if ($loggedIn) {
    $accessStrength = $userType->getAccessStrength();
    // Normal user components
    if ($accessStrength >= UserType::User()->getAccessStrength()) {
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
