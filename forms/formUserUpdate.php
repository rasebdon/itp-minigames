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
    $file = $_FILES['file']['tmp_name'];
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        if (in_array(
            $finfo->file($file),
            array('gif' => "image/gif", 'png' => "image/png", 'jpg' => "image/jpeg", 'jpeg' => "image/jpeg")
        )) { // if extension is allowed
            if (move_uploaded_file($file, "cropped/" . ($newFilename = time() . hexdec(random_bytes(20)) . "." . getExtension($finfo->file($file))))) { // move file
                // resize image, gets saved in function
                $didUpload = true;
            } else {
                $didUpload = false;
            }
        } else {
            $didUpload = false;
        }
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
