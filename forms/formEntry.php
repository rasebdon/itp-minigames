<?php

if (isset($_POST['registerSubmit'])) {
    UserService::$instance->insertUserData($_POST);
}
