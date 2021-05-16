<?php
//for logout
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    setcookie("sessionCookie", "", time() - 60 * 60 * 24, "/");
    unset($_SESSION);
    // unset session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }
    session_destroy();
    header("Location: index.php#");
    exit;
}

if (isset($_POST['ProfilePictureSubmit'])) {
    if (!is_dir("resources/profilePictures/original"))
        mkdir("resources/profilePictures/original");
    if (!is_dir("resources/profilePictures/thumbnail"))
        mkdir("resources/profilePictures/thumbnail");
    if (Validation::$instance->checkMimeType(array("image/gif", "image/png", "image/jpeg", "image/jpeg"), $_FILES['file'])) {
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $newFilename = time() . hexdec(random_bytes(20)) . "." . getExtension($finfo->file($_FILES['file']['tmp_name']));
        $sourcePath = "resources/profilePictures/original/" . $user->getUsername() . $newFilename;
        $thumbnailPath = "resources/profilePictures/thumbnail/" . $user->getUsername() . $newFilename;
        if (PictureService::$instance->uploadImage($_FILES['file'], $sourcePath)) {
            PictureService::$instance->resizeImage($sourcePath, $thumbnailPath, 250, 250);
            ProfilePictureService::$instance->uploadPicture(
                $user->getId(),
                $sourcePath,
                $thumbnailPath
            );
            if (ProfilePictureService::$instance->getDefaultPicture()->getId() != $user->getFK_PictureID()) {
                ProfilePictureService::$instance->deletePicture($user->getFK_PictureID());
            }
            header("Location: index.php?action=editProfile");
            exit;
        }
    } else {
        $_SESSION['profilePictureErrors'] = Validation::$instance->getReturnErrors();
    }
}

function getExtension($mime_type)
{
    $extensions = array(
        'image/gif' => 'gif',
        'image/png' => 'png',
        'image/jpeg' => 'jpeg'
    );
    return $extensions[$mime_type];
}

if (isset($_POST['SubmitSettings'])) {
    if (Validation::$instance->editProfile($_POST, $user->getId())) {
        UserService::$instance->updateProfileData($_POST, $user->getId());
        header("Location: index.php?action=editProfile");
        exit;
    } else {
        $_SESSION['editProfileErrors'] = Validation::$instance->getReturnErrors();
    }
}

if (isset($_POST['SubmitPassword'])) {
    if (Validation::$instance->changePassword($_POST, $user->getUsername())) {
        UserService::$instance->updatePassword($_POST['ConfirmPassword'], $user->getId());
    } else {
        $_SESSION['passwordErrors'] = Validation::$instance->getReturnErrors();
    }
}
