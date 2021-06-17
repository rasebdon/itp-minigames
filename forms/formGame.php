<?php

if (isset($_POST['uploadGame'])) {
    $_SESSION['gameUpload'] = $_POST;
    
    if (Validation::$instance->uploadGame($_POST, $_FILES)) {
        if (isset($_SESSION['uploadGameErrors']))
            unset($_SESSION['uploadGameErrors']);
        if (isset($_SESSION['gameUpload']))
            unset($_SESSION['gameUpload']);

        GameService::$instance->uploadGame();

        //header("Location: index.php");
        exit;
    } else {
        //var_dump($_FILES['game-file-windows']['name']);
        //$fileData['name']
        $_SESSION['uploadGameErrors'] = Validation::$instance->getReturnErrors();
        header("Location: index.php?action=uploadGameInterface");
    }
}

?>