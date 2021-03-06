<?php
class GameService
{
    /** @var GameService  */
    public static $instance;
    /** @var Database  */
    protected $db;
    function __construct(Database $database)
    {
        $this->db = $database;
    }

    /**
     * @return Game[] Array of games
     */
    function getGamesFromUser(int $userID)
    {
        $this->db->query(
            "SELECT *,
            (SELECT AVG(Rating) FROM rating WHERE  rating.FK_GameID = GameID) as Rating
            FROM game
            WHERE FK_UserID = ?;",
            $userID
        );

        $gamesData = $this->db->fetchAll();

        return $this->getGameArrayFromData($gamesData);
    }

    function getGame($gameID)
    {
        $this->db->query(
            "SELECT *,
            (SELECT AVG(Rating) FROM rating WHERE rating.FK_GameID = GameID) as Rating
            FROM game g1
            WHERE GameID = ?;",
            $gameID
        );

        $gameData = $this->db->fetchArray();

        return GameService::$instance->getGameFromData($gameData);
    }

    function getScreenshots($gameID)
    {
        $screenshots = array();

        $this->db->query("SELECT SourcePath
        FROM picture, picture_game
        WHERE picture.PictureID = picture_game.FK_PictureID
        AND picture_game.FK_GameID = ?", $gameID);

        $result = $this->db->fetchAll();

        for ($i = 0; $i < sizeof($result); $i++) {
            $screenshots[$i] = $result[$i]['SourcePath'];
        }

        return $screenshots;
    }

    function addScreenshot($gameID, $file)
    {
        $game = $this->getGame($gameID);

        if (!is_dir("resources/images/"))
            mkdir("resources/images/");
        // Create games dir if it does not exist
        $basePath = "resources/images/games/";
        if (!is_dir($basePath))
            mkdir($basePath);

        // Try to create folder for screenshots of game
        $replaced = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '', $game->getTitle())); // Removes special chars.
        $path = $basePath . $replaced;

        if (!is_dir($path))
            mkdir($path);
        $path .= "/";

        $pathInfo = pathinfo($file["name"]);
        // Generate screenshot name
        $files = scandir($path);
        $name = "image_" . (count($files) - 2);
        // Set target copy path
        $target_file = $path . $name . "." . $pathInfo['extension'];
        $uploadOk = true;
        $mimeType = mime_content_type($file["tmp_name"]);

        // Check file size
        if ($file["size"] > 50000000) {
            echo "Sorry, your image is too large.";
            $uploadOk = false;
        }

        // Allow certain file formats
        if (
            $mimeType != 'image/jpeg' && $mimeType != 'image/png'
        ) {
            echo "Sorry, only jpg/jpeg or png files are allowed.";
            $uploadOk = false;
        }

        // Check if $uploadOk is set to 0 by an error
        if (!$uploadOk) {
            echo "Sorry, your image was not uploaded.";
            exit();
            // if everything is ok, try to upload file
        } else if (!move_uploaded_file($file["tmp_name"], $target_file)) {
            echo "Sorry, there was an error uploading your image.";
            exit();
        }

        // Add database entries
        $this->db->query("INSERT INTO picture (SourcePath, ThumbnailPath) VALUES ( ? , ? )", $target_file, $target_file);
        $this->db->query("INSERT INTO picture_game (FK_GameID, FK_PictureID) VALUES ( ? , ? )", $gameID, $this->db->lastInsertID());
    }

    function getGameFromData($gameData)
    {
        // Null reference catch
        if ($gameData == null)
            return null;

        $user = UserService::$instance->getUser($gameData['FK_UserID']);
        $platforms = $this->getPlatforms($gameData['GameID']);
        $genres = GameService::$instance->getGameGenres($gameData['GameID']);
        $screenshots = GameService::$instance->getScreenshots($gameData['GameID']);

        return new Game(
            $gameData['GameID'],
            $gameData['Name'],
            $user,
            $gameData['Description'],
            $platforms,
            $gameData['Version'],
            $gameData['Rating'] == null ? 0 : $gameData['Rating'],
            $screenshots,
            $gameData['PlayCount'],
            $gameData['Verified'],
            $genres
        );
    }

    function getGameArrayFromData($array)
    {
        // Null reference catch
        if ($array == null || sizeof($array) == 0)
            return array();

        $gameObjs = array();

        for ($i = 0; $i < sizeof($array); $i++) {
            $gameObjs[$i] = GameService::$instance->getGameFromData($array[$i]);
        }

        return $gameObjs;
    }

    function getGameGenres(int $gameID)
    {
        $this->db->query('SELECT *
        FROM game_genre, genre
        WHERE game_genre.FK_GameID = ?
        AND genre.GenreID = game_genre.FK_GenreID', $gameID);
        // Null reference catch -> Return empty array
        if (!($genresData = $this->db->fetchAll()))
            return array();

        $genres = array();
        for ($i = 0; $i < sizeof($genresData); $i++) {
            $genres[$i] = $genresData[$i]["Name"];
        }

        return $genres;
    }

    function searchGames(string $title)
    {
        $baseQuery = "SELECT *, (SELECT AVG(Rating) FROM rating WHERE  rating.FK_GameID = GameID) AS Rating FROM game WHERE `Name` LIKE ?";

        $this->db->query($baseQuery . " AND Verified = 1 ORDER BY GameID ASC", "%" . $title . "%");
        
        // Fetch data
        $gameData = $this->db->fetchAll();
        
        //in case no game found with the title this takes the title and looks for genres and fetches all games with matching genreID
        if($gameData == null){
            $this->db->query("SELECT GenreID from genre WHERE `Name` Like ?", "%" . $title . "%");
            $genreID = $this->db->fetchArray();
            $this->db->query("SELECT * , (SELECT AVG(Rating) FROM rating WHERE  rating.FK_GameID = GameID) AS Rating
            From game LEFT JOIN game_genre ON game.GameID = game_genre.FK_GameID WHERE FK_GenreID = ?", $genreID['GenreID']);
            $gameData = $this->db->fetchALL();
        }
        // Return games array
        return GameService::$instance->getGameArrayFromData($gameData);
    }

    function getGames(int $offset, int $amount, bool $verified = true, bool $all = false)
    {
        $baseQuery = "SELECT *, (SELECT AVG(Rating) FROM rating WHERE  rating.FK_GameID = GameID) AS Rating FROM game";

        if ($all) {
            $query = $baseQuery . " ORDER BY GameID ASC LIMIT ?, ?";
            $this->db->query($query, $offset, $amount);
        } else if ($verified) {
            $query = $baseQuery . " WHERE Verified = 1 ORDER BY GameID ASC LIMIT ?, ?";
            $this->db->query($query, $offset, $amount);
        } else {
            $query = $baseQuery . " WHERE Verified = 0 ORDER BY GameID ASC LIMIT ?, ?";
            $this->db->query($query, $offset, $amount);
        }

        $gameData = $this->db->fetchAll();

        return $this->getGameArrayFromData($gameData);
    }

    function deleteGame($id)
    {
        $game = $this->getGame($id);

        if ($game == null)
            return;

        $gamesPath = "resources/games/" . str_replace(' ', '', $game->getTitle());
        $screenshotsPath = "resources/images/" . str_replace(' ', '', $game->getTitle());


        // Remove from Database
        $screenshots = $game->getScreenshots();
        for ($i = 0; $i < sizeof($screenshots); $i++) {
            $this->db->query("DELETE FROM picture WHERE SourcePath = ?", $screenshots[$i]);
        }

        $this->db->query("DELETE FROM game WHERE GameID = ?", $id);

        // Delete game folders
        try {
            $this->deleteGameFolder($gamesPath);
        } catch (Exception $e) {
            // echo $e->getMessage();
        }

        // Delete pictures folder
        try {
            $this->deleteGameFolder($screenshotsPath);
        } catch (Exception $e) {
            // echo $e->getMessage();
        }
        if(isset($_GET['action']) && $_GET['action'] == "listGamesToVerify")
            echo "<script>location.replace('index.php?action=listGamesToVerify&amount=20&offset=0');</script>";
        else
            echo "<script>location.replace('index.php?action=listCreatedGames');</script>";
    }

    function deleteGameFolder(string $dirPath)
    {
        if (!is_dir($dirPath)) {
            throw new InvalidArgumentException("$dirPath must be a directory");
        }
        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                $this->deleteGameFolder($file);
            } else {
                unlink($file);
            }
        }
        rmdir($dirPath);
    }

    function verifyGame(int $id)
    {
        $this->db->query("UPDATE game SET Verified = 1 WHERE GameID = ?", $id);
    }

    function getGamesCount(bool $verified = true, bool $all = false)
    {
        $baseQuery = "SELECT COUNT(GameID) as Amount  from game";

        if ($all)
            $this->db->query($baseQuery);
        else if ($verified)
            $this->db->query($baseQuery . " WHERE Verified = 1");
        else
            $this->db->query($baseQuery . " WHERE Verified = 0");

        return $this->db->fetchArray()['Amount'];
    }

    /** @return Game[]|null */
    public function getAllGames(bool $verified = true, bool $all = false)
    {
        $baseQuery = "SELECT *, (SELECT AVG(Rating) FROM rating WHERE  rating.FK_GameID = GameID) AS Rating FROM game";

        if ($all)
            $this->db->query($baseQuery);
        else if ($verified)
            $this->db->query($baseQuery . " WHERE Verified = 1");
        else
            $this->db->query($baseQuery . " WHERE Verified = 0");

        $gameData = $this->db->fetchAll();

        return $this->getGameArrayFromData($gameData);
    }

    public function getGameByForumId(int $forumid)
    {
        $this->db->query("SELECT *, (SELECT AVG(Rating) FROM rating WHERE  rating.FK_GameID = GameID) AS Rating from game WHERE FK_ForumID = ?", $forumid);
        $gameData = $this->db->fetchArray();
        return $this->getGameFromData($gameData);
    }

    public function getForumID(Game $game)
    {
        $this->db->query("SELECT FK_ForumID from game where GameID = ?", $game->getId());
        return $this->db->fetchArray()['FK_ForumID'];
    }

    function getPlatforms($gameID)
    {
        $this->db->query("SELECT platform.Name FROM platform
        LEFT JOIN game_platform ON game_platform.FK_PlatformID = platform.PlatformID
        LEFT JOIN game ON game_platform.FK_GameID = game.GameID
        WHERE game.GameID = $gameID");
        // Null reference catch -> Return empty array
        if (!($platformsData = $this->db->fetchAll()))
            return array();

        $platforms = array();

        for ($i = 0; $i < sizeof($platformsData); $i++) {
            $platforms[$platformsData[$i]['Name']] = true;
        }

        return $platforms;
    }

    function getAllPlatforms()
    {
        $this->db->query("SELECT * FROM platform ORDER BY PlatformID ASC;");
        return $this->db->fetchAll();
    }

    /**
     * Returns all game genres in the following format (ASC by ID):
     * 
     * array
     *   X =>
     *      array (size=2)
     *          'GenreID' => int
     *          'Name' => string
     *
     */
    function getAllGenres()
    {
        $this->db->query("SELECT * FROM genre ORDER BY GenreID ASC;");
        return $this->db->fetchAll();
    }

    function uploadGameFile($file, string $sourcePath, string $gameVersion, Platform $platform)
    {
        $pathInfo = pathinfo($file["name"]);
        $target_file = $sourcePath . $gameVersion . "_" . $platform->name . "." . $pathInfo['extension'];
        $uploadOk = true;
        $mimeType = mime_content_type($file["tmp_name"]);

        // Check if file already exists
        if (file_exists($target_file)) {
            echo "Sorry, game version already exists.";
            $uploadOk = false;
        }

        // Check file size
        if ($file["size"] > 50000000000) {
            echo "Sorry, your game is too large.";
            $uploadOk = false;
        }

        // Allow certain file formats
        if (
            $mimeType != 'application/zip' && $mimeType != 'application/x-rar-compressed'
            && $mimeType != 'application/x-rar' && $mimeType != 'application/x-7z-compressed'
            && $mimeType != 'application/x-7z' && $mimeType != 'application/x-tar'
            && $mimeType != 'application/x-gtar'
        ) {
            echo "Sorry, only zip or rar files are allowed.";
            $uploadOk = false;
        }

        // Check if $uploadOk is set to 0 by an error
        if (!$uploadOk) {
            echo "Sorry, your game was not uploaded.";
            exit();
            // if everything is ok, try to upload file
        } else if (!move_uploaded_file($file["tmp_name"], $target_file)) {
            echo "Sorry, there was an error uploading your file.";
            exit();
        }
    }

    function editGame()
    {
        $gameID = $_POST['game-id'];
        $oldData = GameService::$instance->getGame($gameID);

        // Need to check that if something is uploaded,
        // that the version is newer

        // Edit game
        // Check for platforms
        $windowsFile = $_FILES["game-file-windows"];
        $linuxFile = $_FILES["game-file-linux"];
        $macFile = $_FILES["game-file-mac"];

        // Upload games
        $platforms = array();

        $sourcePath = "resources/games/" . str_replace(' ', '', $oldData->getTitle()) . "/";

        if (isset($windowsFile) && $windowsFile != null && $windowsFile['error'] == 0) {
            $this->uploadGameFile($windowsFile, $sourcePath, $_POST['game-version'], Platform::Windows());
            $platforms[sizeof($platforms)] = Platform::Windows()->id;
        }
        if (isset($linuxFile) && $linuxFile != null && $linuxFile['error'] == 0) {
            $this->uploadGameFile($linuxFile, $sourcePath, $_POST['game-version'], Platform::Linux());
            $platforms[sizeof($platforms)] = Platform::Linux()->id;
        }
        if (isset($macFile) && $macFile != null && $macFile['error'] == 0) {
            $this->uploadGameFile($macFile, $sourcePath, $_POST['game-version'], Platform::Mac());
            $platforms[sizeof($platforms)] = Platform::Mac()->id;
        }

        $now = new DateTime('now');
        $now = $now->format("Y-m-d H:m:s");

        // Update game data
        $this->db->query(
            "UPDATE `game`
            SET `Description` = ?, `Version` = ?, `UpdateDate` = ?
            WHERE `game`.`GameID` = ?",
            $_POST['game-description'],
            $_POST['game-version'],
            $now,
            $gameID
        );

        // Update genres
        if (isset($_POST['game-genres'])) {
            // First delete genres
            $this->db->query("DELETE FROM game_genre WHERE FK_GameID = ?", $gameID);
            // Insert genres
            $genres = $_POST['game-genres'];
            for ($i = 0; $i < sizeof($genres); $i++) {
                $this->db->query("INSERT INTO game_genre VALUES ( ? , ? )", $gameID, $genres[$i]);
            }
        } else {
            $this->db->query("DELETE FROM game_genre WHERE FK_GameID = ?", $gameID);
        }

        // Check for uploaded screenshots

        // // Re array multiple file upload
        // $images = $this->ReArrayFiles($_FILES['image-files']);

        // // Add screenshots
        // for ($i = 0; $i < sizeof($images); $i++) {
        //     $this->addScreenshot($gameID, $images[$i]);
        // }

        // Insert platforms
        for ($i=0; $i < sizeof($platforms); $i++) { 
            $this->db->query("INSERT INTO game_platform VALUES ( ? , ? )", $gameID, $platforms[$i]);
        }

        // Also auto redirect possible
        echo "<script>location.replace('index.php?action=editGameInterface&id=$gameID');</script>";
    }

    function uploadGame()
    {
        $userID = $_SESSION['UserID'];

        // Strip whitespaces from title at the end
        $title = trim($_POST['game-title']);

        try {
            $this->db->query("SELECT COUNT(*) AS 'Count' FROM `game` WHERE `Name` = ?", $title);
            $gameCount = $this->db->fetchArray()['Count'];
            if($gameCount != 0) {
                echo "A game with this name already exists. Please rename your game and try again!";
                return;
            }

        }
        catch (Exception $ex) {
            // ONLY DEBUG MODE
            // echo $ex->getMessage()
        }

        // Upload game
        // Check for platforms
        $windowsFile = $_FILES["game-file-windows"];
        $linuxFile = $_FILES["game-file-linux"];
        $macFile = $_FILES["game-file-mac"];

        // Quit if there is no game attached 
        if ((!isset($windowsFile) && !isset($linuxFile) && !isset($macFile)) ||
            ($windowsFile['error'] != 0 && $linuxFile['error'] != 0 && $macFile['error'] != 0)
        ) {
            echo "Please attach a game file and try again.";
            return;
        }

        // Create games dir if it does not exist
        if (!is_dir("resources/games"))
            mkdir("resources/games");

        $replaced = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '', $_POST['game-title'])); // Removes special chars.
        $sourcePath = "resources/games/" . $replaced;
        mkdir($sourcePath);
        $sourcePath .= "/";

        $platforms = array();

        if (isset($windowsFile) && $windowsFile != null && $windowsFile['error'] == 0) {
            $this->uploadGameFile($windowsFile, $sourcePath, $_POST['game-version'], Platform::Windows());
            $platforms[sizeof($platforms)] = Platform::Windows()->id;
        }
        if (isset($linuxFile) && $linuxFile != null && $linuxFile['error'] == 0) {
            $this->uploadGameFile($linuxFile, $sourcePath, $_POST['game-version'], Platform::Linux());
            $platforms[sizeof($platforms)] = Platform::Linux()->id;
        }
        if (isset($macFile) && $macFile != null && $macFile['error'] == 0) {
            $this->uploadGameFile($macFile, $sourcePath, $_POST['game-version'], Platform::Mac());
            $platforms[sizeof($platforms)] = Platform::Mac()->id;
        }

        // Create forum
        $this->db->query("INSERT INTO forum VALUES (NULL)");
        $forumID = $this->db->lastInsertID();

        $now = new DateTime('now');
        $now = $now->format("Y-m-d H:m:s");

        // Insert data into database
        $this->db->query(
            "INSERT INTO `game` 
            (`GameID`, `FK_UserID`, `FK_ForumID`, `Name`,
            `Description`, `Version`, `UpdateDate`, `UploadDate`,
            `PlayCount`, `Verified`, `SourcePath`)
            VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ",
            $userID,
            $forumID,
            $title,
            $_POST['game-description'],
            $_POST['game-version'],
            $now,
            $now,
            0,
            0,
            null
        );

        $gameID = $this->db->lastInsertID();

        // Insert genres
        if (isset($_POST['game-genres'])) {
            $genres = $_POST['game-genres'];
            for ($i = 0; $i < sizeof($genres); $i++) {
                $this->db->query("INSERT INTO game_genre VALUES ( ? , ? )", $gameID, $genres[$i]);
            }
        }

        // Insert platforms
        for ($i = 0; $i < sizeof($platforms); $i++) {
            $this->db->query("INSERT INTO game_platform VALUES ( ? , ? )", $gameID, $platforms[$i]);
        }

        // // Re array multiple file upload
        // $files = $this->ReArrayFiles($_FILES['image-files']);

        // // Add screenshots
        // for ($i = 0; $i < sizeof($files); $i++) {
        //     $this->addScreenshot($gameID, $files[$i]);
        // }

        // Also auto redirect possible
        echo "<script>location.replace('index.php?action=viewGame&id=$gameID');</script>";
        // echo "<h3>Game upload succesful!</h3><a class='btn btn-primary' href='index.php?action=viewGame&id=$gameID'>View Game</a>";
    }

    function ReArrayFiles(&$file_post)
    {
        $isMulti = is_array($file_post['name']);
        $file_count = $isMulti ? count($file_post['name']) : 1;
        $file_keys = array_keys($file_post);

        $file_ary = [];
        for ($i = 0; $i < $file_count; $i++)
            foreach ($file_keys as $key)
                if ($isMulti)
                    $file_ary[$i][$key] = $file_post[$key][$i];
                else
                    $file_ary[$i][$key] = $file_post[$key];

        return $file_ary;
    }

    function insertRating($gameid, $userid, $rating)
    {
        $this->db->query(
            "REPLACE INTO rating (FK_UserID, FK_GameID, Rating) VALUES (?, ?, ?)",
            $userid,
            $gameid,
            $rating
        );
    }

    function getRatingByStars($gameid, $stars)
    {
        if ($stars > 5 || $stars < 1) {
            return;
        }
        $this->db->query("SELECT COUNT(*) FROM rating WHERE FK_GameID = ? AND Rating = ?", $gameid, $stars);

        return $this->db->fetchAll()[0]['COUNT(*)'];
    }



    function getFavorites($userID)
    {
        $this->db->query("SELECT *,
        (SELECT AVG(Rating) FROM rating WHERE rating.FK_GameID = GameID) as Rating
        FROM `favorite`
        LEFT JOIN game ON favorite.FK_GameID = game.GameID
        WHERE favorite.FK_UserID = ?", $userID);

        $gameData = $this->db->fetchAll();

        return GameService::$instance->getGameArrayFromData($gameData);
    }

    function deleteScreenshot($screenshotPath)
    {
        // Remove db entry
        $this->db->query("DELETE FROM picture WHERE SourcePath = ?", $screenshotPath);
        // Delete file
        unlink($screenshotPath);
    }
}

GameService::$instance = new GameService(Database::$instance);
