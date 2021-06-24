<?php
class PictureUploadService
{
    /** @var PictureUploadService  */
    public static $instance;
    /** @var Database  */
    protected $db;
    function __construct(Database $database)
    {
        $this->db = $database;
    }

    function uploadImage($file, $path)
    {
        $fileTmp = $file['tmp_name'];
        if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
            // if extension is allowed
            if (move_uploaded_file($fileTmp, $path)) { // move file
                // resize image, gets saved in function
                return true;
            }
            return false;
        }
    }

    function resizeImage($file, $target, $w, $h)
    {
        list($width, $height) = getimagesize($file); // get original size
        if ($width > $w || $height > $h) {  // only resize if image is big enough
            $r = $width / $height; // calculate aspect ratio
            // calculate new size
            if ($w / $h > $r) {
                $new_width = $h * $r;
                $new_height = $h;
            } else {
                $new_height = $w / $r;
                $new_width = $w;
            }
        } else {
            $new_height = $height;
            $new_width = $width;
        }
        $image_info = getimagesize($file);
        $image_type = $image_info[2];
        switch ($image_type) {
            case IMAGETYPE_JPEG:
                $src = imagecreatefromjpeg($file);
                break;
            case IMAGETYPE_GIF:
                $src = imagecreatefromgif($file); // return image with play button, use lightbox to display actual gif
                break;
            case IMAGETYPE_PNG:
                $src = imagecreatefrompng($file);
                break;
            default:
                // shouldn't get here
                break;
        }
        // create new image, save  to $target (pictures/thumbnails/...)
        $dst = imagecreatetruecolor($new_width, $new_height);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
        imagejpeg($dst, $target);
        return $dst;
    }
}

PictureUploadService::$instance = new PictureUploadService(Database::$instance);