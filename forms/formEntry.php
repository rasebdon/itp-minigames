<?php

if (isset($_POST['RegisterSubmit'])) {
    $_SESSION['register'] = $_POST; // store post data in session to keep data after refresh (sending form) 
    // validate input
    if (Validation::$instance->register($_POST)) {
        // clear possible previous error messages
        if (isset($_SESSION['registerErrors']))
            unset($_SESSION['registerErrors']);

        // insert into database...
        UserService::$instance->insertUserData($_POST);
    } else {
        // get errors and store into session variable
        $_SESSION['registerErrors'] = Validation::$instance->getReturnErrors();
    }
}
