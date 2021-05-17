<?php
class ProfilePictureService
{
    /** @var UserService  */
    public static $instance;
    /** @var Database  */
    protected $db;
    function __construct(Database $database)
    {
        $this->db = $database;
    }

    public function getPicture($pid)
    {
        $this->db->query("SELECT * FROM picture WHERE PictureID = ?", $pid);
        $picture = $this->db->fetchArray();
        return new Picture(
            $picture['PictureID'],
            $picture['SourcePath'],
            $picture['ThumbnailPath']
        );
    }

    public function uploadPicture($uid, $sourcePath, $thumbnailPath)
    {
        $this->db->query(
            "INSERT INTO picture (SourcePath, ThumbnailPath) VALUES (?, ?)",
            $sourcePath,
            $thumbnailPath,
        );
        $this->db->query("UPDATE user SET FK_PictureID = ? WHERE UserID = ?", $this->db->lastInsertID(), $uid);
    }

    public function deletePicture($pid)
    {
        $picture = $this->getPicture($pid);
        $thumbnail_path = $picture->getThumbnailPath();
        if (file_exists($thumbnail_path)) {
            unlink($thumbnail_path);
        }
        $source_path = $picture->getSourcePath();
        if (file_exists($source_path)) {
            unlink($source_path);
        }
        $this->db->query("DELETE FROM Picture WHERE PictureID = ?", $pid);
    }

    public function getDefaultPicture()
    {
        $this->db->query("SELECT * FROM picture WHERE SourcePath LIKE 'resources/profilePictures/default%'");
        $picture = $this->db->fetchArray();
        return new Picture(
            $picture['PictureID'],
            $picture['SourcePath'],
            $picture['ThumbnailPath']
        );
    }
    // public function removeImage($iid)
    // {
    //     $image = $this->getImage($iid);
    //     echo "remove";

    //     $thumbnail_path = $image->getThumbnail_path();
    //     if (file_exists($thumbnail_path)) {
    //         unlink($thumbnail_path);
    //     }
    //     $source_path =  $image->getSource_path();
    //     if (file_exists($source_path)) {
    //         unlink($source_path);
    //     }
    // }
}

ProfilePictureService::$instance = new ProfilePictureService(Database::$instance);
