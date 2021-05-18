<?php

class Initialization {

    public static $defaultAdminUsername = "admin";
    public static $defaultAdminPassword = "admin";

    public static function RunConfiguration() {
        if(!Initialization::DatabaseCheck())
            exit();
    }

    /**
     * Checks the database for needed entries and resources
     */
    public static function DatabaseCheck() {
        try {
            // Check if database is initialized!
            if(Database::$instance == null) {
                throw new Exception("Database not initialized! Construct Database::\$instance!", 70);
            }
            // Check if database is complete
            Database::$instance->query("SELECT * 
            FROM information_schema.tables
            WHERE table_schema = 'itp-minigames'");
            $result = Database::$instance->fetchAll();
            if(sizeof($result) != 18)
                throw new Exception("Database not complete! Please add all tables that are needed to database!", 71);
            
            // Check if platforms are correctly set up
            Database::$instance->query("SELECT * FROM `platform` ORDER BY PlatformID ASC");
            $result = Database::$instance->fetchAll();
            if(sizeof($result) != 3)
                throw new Exception("Platform table not correctly set up!", 72);
            else {
                if($result[0]['PlatformID'] != 1 || $result[0]['Name'] != "Windows")
                    throw new Exception("Platform table not correctly set up!", 73);
                if($result[1]['PlatformID'] != 2 || $result[1]['Name'] != "Linux")
                    throw new Exception("Platform table not correctly set up!", 74);
                if($result[2]['PlatformID'] != 3 || $result[2]['Name'] != "Mac OS")
                    throw new Exception("Platform table not correctly set up!", 75);
            }
            
            // Check if default profile picture path is set
            Database::$instance->query("SELECT `PictureID`, `SourcePath`, `ThumbnailPath`
            FROM `picture` WHERE `SourcePath` = 'resources/profilePictures/default.png'
            AND `ThumbnailPath` = 'resources/profilePictures/defaultThumbnail.jpeg';");
            $result = Database::$instance->fetchAll();
            if(sizeof($result) == 0)
                throw new Exception("No default profile picture database entry found! Inserting default path.", 76);

            // Check for available admin user
            Database::$instance->query("SELECT * FROM `user` WHERE `Usertype` = 'admin'");
            $result = Database::$instance->fetchAll();
            if(sizeof($result) == 0)
                throw new Exception("No administrator user found! Inserting default administrator.", 77);
        }
        catch(Exception $e) {
            switch($e->getCode()) {
                // Invalid platform
                case 72:
                case 73:
                case 74:
                case 75:
                    // Autogenerate the platform table
                    // Clear table platform
                    Database::$instance->query("DELETE FROM `platform`");
                    // Reset auto increment value
                    Database::$instance->query("ALTER TABLE `platform` AUTO_INCREMENT = 1");
                    // Insert correct platforms 
                    Database::$instance->query("INSERT INTO `platform` (`PlatformID`, `Name`)
                    VALUES ('1', 'Windows'), ('2', 'Linux'), ('3', 'Mac OS')");
                    break;
                case 76:
                    Database::$instance->query("INSERT INTO `picture`(`SourcePath`, `ThumbnailPath`) VALUES ('resources/profilePictures/default.png','resources/profilePictures/defaultThumbnail.jpeg')");
                    break;
                case 77:
                    // Get id of default profile picture
                    Database::$instance->query("SELECT `PictureID`
                    FROM `picture` WHERE `SourcePath` = 'resources/profilePictures/default.png'
                    AND `ThumbnailPath` = 'resources/profilePictures/defaultThumbnail.jpeg';");
                    $result = Database::$instance->fetchArray();
                    $pictureId = $result['PictureID'];
                    $password = password_hash(Initialization::$defaultAdminPassword, PASSWORD_DEFAULT);
                    $username = Initialization::$defaultAdminUsername;
                    Database::$instance->query("INSERT INTO `user` (`FirstName`, `LastName`, `Username`, `Email`, `Usertype`, `Password`, `FK_PictureID`) VALUES ('admin', 'admin', '$username', 'admin@admin.admin', 'admin', '$password', $pictureId)");
                    break;
            }
            throw new Exception($e->getMessage(), $e->getCode());
            return false; 
        }
        // Return true if everything is correctly set up
        return true;
    }
}

// Check for right setup of page
Initialization::RunConfiguration();