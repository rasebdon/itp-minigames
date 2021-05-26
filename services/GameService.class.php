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

    function getGameFromData($gameData)
    {
        // Null reference catch
        if ($gameData == null)
            return null;

        $user = UserService::$instance->getUser($gameData['FK_UserID']);
        $platforms = $this->getPlatforms($gameData['GameID']);
        $genres = GameService::$instance->getGameGenres($gameData['GameID']);

        return new Game(
            $gameData['GameID'],
            $gameData['Name'],
            $user,
            $gameData['Description'],
            $platforms,
            $gameData['Version'],
            $gameData['Rating'] == null ? 0 : $gameData['Rating'],
            array(), // TODO !
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

    function searchGames(string $title, bool $verified = true, bool $all = false)
    {
        $baseQuery = "SELECT *, (SELECT AVG(Rating) FROM rating WHERE  rating.FK_GameID = GameID) AS Rating FROM game WHERE `Name` LIKE ?";

        if ($all) {
            $this->db->query($baseQuery . " ORDER BY GameID ASC", "%" . $title . "%");
        } else if ($verified) {
            $this->db->query($baseQuery . " AND Verified = 1 ORDER BY GameID ASC", "%" . $title . "%");
        } else {
            $this->db->query($baseQuery . " AND Verified = 0 ORDER BY GameID ASC", "%" . $title . "%");
        }
        // Fetch data
        $gameData = $this->db->fetchAll();
        // Return games array
        return GameService::$instance->getGameArrayFromData($gameData);
    }

    function getGames(int $offset, int $amount, bool $verified = true, bool $all = false)
    {
        $baseQuery = "SELECT *, (SELECT AVG(Rating) FROM rating WHERE  rating.FK_GameID = GameID) AS Rating FROM game";

        if ($all) {
            $query = $baseQuery . " ORDER BY GameID ASC LIMIT ?, ?";
            $this->db->query($query, $offset, $amount);
        }
        else if ($verified) {
            $query = $baseQuery . " WHERE Verified = 1 ORDER BY GameID ASC LIMIT ?, ?";
            $this->db->query($query, $offset, $amount);
        }
        else {
            $query = $baseQuery . " WHERE Verified = 0 ORDER BY GameID ASC LIMIT ?, ?";
            $this->db->query($query, $offset, $amount);
        }

        $gameData = $this->db->fetchAll();

        return $this->getGameArrayFromData($gameData);
    }

    function deleteGame(int $id)
    {
        $game = $this->getGame($id);

        if ($game == null)
            return;

        $dirPath = "resources/games/" . str_replace(' ', '', $game->getTitle());

        // Delete Games
        try {
            $this->deleteGameFolder($dirPath);
        } catch (Exception $e) {
            // echo $e->getMessage();
        }

        // Remove from Database
        $this->db->query("DELETE FROM game WHERE GameID = ?", $id);
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
        $this->db->query("SELECT * from game WHERE FK_ForumID = ?", $forumid);
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
        if ($file["size"] > 500000) {
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

    function editGame() {
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

        if(isset($windowsFile) && $windowsFile != null && $windowsFile['error'] == 0) {
            $this->uploadGameFile($windowsFile, $sourcePath, $_POST['game-version'], Platform::Windows());
            $platforms[sizeof($platforms)] = Platform::Windows()->id;
        }
        if(isset($linuxFile) && $linuxFile != null && $linuxFile['error'] == 0) {
            $this->uploadGameFile($linuxFile, $sourcePath, $_POST['game-version'], Platform::Linux());
            $platforms[sizeof($platforms)] = Platform::Linux()->id;
        }
        if(isset($macFile) && $macFile != null && $macFile['error'] == 0) {
            $this->uploadGameFile($macFile, $sourcePath, $_POST['game-version'], Platform::Mac());
            $platforms[sizeof($platforms)] = Platform::Mac()->id;
        }

        $now = new DateTime('now');
        $now = $now->format("Y-m-d H:m:s");

        // Update game data
        $this->db->query("UPDATE `game`
        SET `Description` = ?, `Version` = ?, `UpdateDate` = ?
        WHERE `game`.`GameID` = ?", $_POST['game-description'],
        $_POST['game-version'], $now, $gameID);

        // Update genres
        if(isset($_POST['game-genres'])) {
            // First delete genres
            $this->db->query("DELETE FROM game_genre WHERE FK_GameID = ?", $gameID);
            // Insert genres
            $genres = $_POST['game-genres'];
            for ($i=0; $i < sizeof($genres); $i++) { 
                $this->db->query("INSERT INTO game_genre VALUES ( ? , ? )", $gameID, $genres[$i]);
            }
        }

        // Check which games were uploaded and update platforms
        // // Insert platforms
        // for ($i=0; $i < sizeof($platforms); $i++) { 
        //     $this->db->query("INSERT INTO game_platform VALUES ( ? , ? )", $gameID, $platforms[$i]);
        // }

        // Also auto redirect possible
        echo "<h3>Game edit succesful!</h3><a class='btn btn-primary' href='index.php?action=viewGame&id=$gameID'>View Game</a>";
    }

    function uploadGame() {
        $userID = $_SESSION['UserID'];

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

        $sourcePath = "resources/games/" . str_replace(' ', '', $_POST['game-title']);
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
            $_POST['game-title'],
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

        // Also auto redirect possible
        echo "<h3>Game upload succesful!</h3><a class='btn btn-primary' href='index.php?action=viewGame&id=$gameID'>View Game</a>";
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
}

GameService::$instance = new GameService(Database::$instance);
