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
    function getGamesFromUser(int $userID) {
        // Get the author
        $user = UserService::$instance->GetUser($userID);
        $games = array();

        $this->db->query(
            "SELECT *,
            (SELECT AVG(Rating) FROM rating WHERE  rating.FK_GameID = GameID) as Rating
            FROM game
            WHERE FK_UserID = ?;", $userID);
        // Null reference catch -> Return empty array
        if (!($gamesData = $this->db->fetchAll()))
            return $games;

        for ($i = 0; $i < sizeof($gamesData); $i++) {
            $gameID = $gamesData[$i]['GameID'];
            $platforms = $this->getPlatforms($gameID);
            $genres = $this->getGameGenres($gameID);

            $games[$i] = new Game(
                $gameID,
                $gamesData[$i]['Name'],
                $user,
                $gamesData[$i]['Description'],
                $platforms,
                $gamesData[$i]['Version'],
                $gamesData[$i]['Rating'] == null ? 0 : $gamesData[$i]['Rating'],
                array(), // TODO !
                $gamesData[$i]['PlayCount'],
                $gamesData[$i]['Verified'],
                $genres
            );
        }
        return $games;
    }

    function getGameGenres(int $gameID) {
        $this->db->query('SELECT *
        FROM game_genre, genre
        WHERE game_genre.FK_GameID = ?
        AND genre.GenreID = game_genre.FK_GenreID', $gameID);
        // Null reference catch -> Return empty array
        if (!($genresData = $this->db->fetchAll()))
            return null;

        $genres = array();
        for ($i=0; $i < sizeof($genresData); $i++) { 
            $genres[$i] = $genresData[$i]["Name"];
        }

        return $genres;
    }

    function getGame(int $gameID) {
        $this->db->query(
            "SELECT *,
            (SELECT AVG(Rating) FROM rating WHERE rating.FK_GameID = GameID) as Rating
            FROM game g1
            WHERE GameID = ?;", $gameID);
        // Null reference catch -> Return empty array
        if (!($gameData = $this->db->fetchArray()))
            return null;
        
        $user = UserService::$instance->getUser($gameData['FK_UserID']);
        $platforms = $this->getPlatforms($gameID);
        $genres = $this->getGameGenres($gameID);

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
    function genreNameToGameData(string $genre){
        $this->db->query("SELECT GenreID from genre WHERE `Name` Like ?", "%" . $genre . "%");
        $genreID = $this->db->fetchArray();
        $this->db->query("SELECT * from game LEFT JOIN game_genre ON game.GameID = game_genre.FK_GameID WHERE FK_GenreID = ?", $genreID['GenreID']);
        $result = $this->db->fetchALL();
        //print_r($result);//debug
        return $result;
    }

    
  
    function searchGames(string $title, bool $verified = true, bool $all = false) {
        
        if($all){
            $this->db->query("SELECT * from game WHERE `Name` LIKE ? ORDER BY GameID ASC", "%" . $title . "%");
            $gameData = $this->db->fetchAll();
        }
        else if($verified){
            $this->db->query("SELECT *,
            (SELECT AVG(Rating) FROM rating WHERE rating.FK_GameID = GameID) as Rating
            FROM game
            WHERE `Name` LIKE ?
            AND Verified = 1
            ORDER BY GameID ASC", "%" . $title . "%");

            if (!($gameData = $this->db->fetchAll())){
               $gameData = $this->genreNameToGameData($title);
               /*Array returned from genreNameToGameData() looks like this
                Array ( 
                    [0] => Array ( 
                        [GameID] => 13 
                        [FK_UserID] => 105 
                        [FK_ForumID] => 13 
                        [Name] => test6 
                        [Description] => 6 
                        [Version] => 6 
                        [UpdateDate] => 2021-05-12 12:05:10 
                        [UploadDate] => 2021-05-12 12:05:10 
                        [PlayCount] => 0 
                        [Verified] => 1 
                        [SourcePath] => 
                        [FK_GameID] => 13 
                        [FK_GenreID] => 5 ) )*/
            }
        }
        else{
            $this->db->query("SELECT * from game WHERE `Name` LIKE ? AND Verified = 0 ORDER BY GameID ASC", "%" . $title . "%");
            $gameData = $this->db->fetchAll();
        }

        // Null reference catch
        if (!($gameData))
        return null;

        $gameObjs = array();

        for ($i = 0; $i < sizeof($gameData); $i++) {
            $gameID = $gameData[$i]['GameID'];
            $platforms = $this->getPlatforms($gameID);
            $genres = $this->getGameGenres($gameID);
            $user = UserService::$instance->getUser($gameData[$i]['FK_UserID']);

            $gameObjs[$i] = new Game(
                $gameID,
                $gameData[$i]['Name'],
                $user,
                $gameData[$i]['Description'],
                $platforms,
                $gameData[$i]['Version'],
                $gameData[$i]['Rating'],
                array(), 
                $gameData[$i]['PlayCount'],
                $gameData[$i]['Verified'],
                $genres
                
            );
        }
        return $gameObjs; 
    }

    function getGames(int $offset, int $amount, bool $verified = true, bool $all = false) {
        if($all)
            $this->db->query("SELECT *,
            (SELECT AVG(Rating) FROM rating WHERE rating.FK_GameID = GameID) as Rating from game ORDER BY GameID ASC LIMIT ?, ?", $offset, $amount);
        else if($verified)
            $this->db->query("SELECT *,
            (SELECT AVG(Rating) FROM rating WHERE rating.FK_GameID = GameID) as Rating from game WHERE Verified = 1 ORDER BY GameID ASC LIMIT ?, ?", $offset, $amount);
        else
            $this->db->query("SELECT *,
            (SELECT AVG(Rating) FROM rating WHERE rating.FK_GameID = GameID) as Rating from game WHERE Verified = 0 ORDER BY GameID ASC LIMIT ?, ?", $offset, $amount);
    
        // Null reference catch
        if (!($gameData = $this->db->fetchAll()))
            return null;

        $gameObjs = array();

        for ($i = 0; $i < sizeof($gameData); $i++) {
            $gameID = $gameData[$i]['GameID'];
            $platforms = $this->getPlatforms($gameID);
            $genres = $this->getGameGenres($gameID);
            $user = UserService::$instance->getUser($gameData[$i]['FK_UserID']);

            $gameObjs[$i] = new Game(
                $gameID,
                $gameData[$i]['Name'],
                $user,
                $gameData[$i]['Description'],
                $platforms,
                $gameData[$i]['Version'],
                $gameData[$i]['Rating'],
                array(), 
                $gameData[$i]['PlayCount'],
                $gameData[$i]['Verified'],
                $genres
            );
        }
        return $gameObjs; 
    }

    function deleteGame(int $id) {
        $game = $this->getGame($id);

        if($game == null)
            return;

        $dirPath = "resources/games/" . str_replace(' ', '', $game->getTitle());

        // Delete Games
        try {
            $this->deleteGameFolder($dirPath);
        }
        catch(Exception $e) {
            // echo $e->getMessage();
        }

        // Remove from Database
        $this->db->query("DELETE FROM game WHERE GameID = ?", $id);
    }

    function deleteGameFolder(string $dirPath) {
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

    function verifyGame(int $id) {
        $this->db->query("UPDATE game SET Verified = 1 WHERE GameID = ?", $id);
    }

    function getGamesCount(bool $verified = true, bool $all = false) {
        if($all)
            $this->db->query("SELECT COUNT(GameID) as Amount  from game");
        else if($verified)
            $this->db->query("SELECT COUNT(GameID) as Amount  from game WHERE Verified = 1");
        else
            $this->db->query("SELECT COUNT(GameID) as Amount from game WHERE Verified = 0");
        return $this->db->fetchArray()['Amount'];
    }

    public function getAllGames(bool $verified = true, bool $all = false){
        if($all) {
            $this->db->query("SELECT *,
            (SELECT AVG(Rating) FROM rating WHERE rating.FK_GameID = GameID) as Rating from game");
        
        } else if($verified) {
            $this->db->query("SELECT *,
            (SELECT AVG(Rating) FROM rating WHERE rating.FK_GameID = GameID) as Rating from game WHERE Verified = 1");
        
        } else {
            $this->db->query("SELECT *,
            (SELECT AVG(Rating) FROM rating WHERE rating.FK_GameID = GameID) as Rating from game WHERE Verified = 0");
        }

        // Null reference catch
        if (!($gameData = $this->db->fetchAll()))
            return null;

        $gameObjs = array();

        for ($i = 0; $i < sizeof($gameData); $i++) {
            $gameID = $gameData[$i]['GameID'];
            $platforms = $this->getPlatforms($gameID);
            $genres = $this->getGameGenres($gameID);
            $user = UserService::$instance->getUser($gameData[$i]['FK_UserID']);

            $gameObjs[$i] = new Game(
                $gameID,
                $gameData[$i]['Name'],
                $user,
                $gameData[$i]['Description'],
                $platforms,
                $gameData[$i]['Version'],
                $gameData[$i]['Rating'],
                array(), 
                $gameData[$i]['PlayCount'],
                $gameData[$i]['Verified'],
                $genres
            );
        }
        return $gameObjs; 
    }

    public function getGameByForumId(int $forumid) {   
        $this->db->query("SELECT *,
        (SELECT AVG(Rating) FROM rating WHERE rating.FK_GameID = GameID) as Rating  from game WHERE FK_ForumID = ?", $forumid);

        if (!($gameData = $this->db->fetchArray()))
            return null;

        $gameID = $gameData['GameID'];
        $platforms = $this->getPlatforms($gameID);
        $genres = $this->getGameGenres($gameID);
        $user = UserService::$instance->getUser($gameData['FK_UserID']);


        $gameObj = new Game(
            $gameData['GameID'],
            $gameData['Name'],
            $user,
            $gameData['Description'],
            $platforms,
            $gameData['Version'],
            $gameData['Rating'],
            array(), 
            $gameData['PlayCount'],
            $gameData['Verified'],
            $genres
        );

        return $gameObj;

    }

    public function getForumID(Game $game){
        $this->db->query("SELECT FK_ForumID from game where GameID = ?", $game->getId());
        return $this->db->fetchArray()['FK_ForumID'];
    }

    function getPlatforms($gameID) {
        $this->db->query("SELECT platform.Name FROM platform
        LEFT JOIN game_platform ON game_platform.FK_PlatformID = platform.PlatformID
        LEFT JOIN game ON game_platform.FK_GameID = game.GameID
        WHERE game.GameID = $gameID");
        // Null reference catch -> Return empty array
        if (!($platformsData = $this->db->fetchAll()))
            return array();
        
        $platforms = array();

        for ($i=0; $i < sizeof($platformsData); $i++) { 
            $platforms[$platformsData[$i]['Name']] = true;
        }

        return $platforms;
    }

    function getAllPlatforms() {
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
    function getAllGenres() {
        $this->db->query("SELECT * FROM genre ORDER BY GenreID ASC;");
        return $this->db->fetchAll();
    }

    function uploadGameFile($file, string $sourcePath, string $gameVersion, Platform $platform) {
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
        if($mimeType != 'application/zip' && $mimeType != 'application/x-rar-compressed'
        && $mimeType != 'application/x-rar' && $mimeType != 'application/x-7z-compressed'
        && $mimeType != 'application/x-7z' && $mimeType != 'application/x-tar'
        && $mimeType != 'application/x-gtar') {
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
        if((!isset($windowsFile) && !isset($linuxFile) && !isset($macFile)) || 
        ($windowsFile['error'] != 0 && $linuxFile['error'] != 0 && $macFile['error'] != 0)) { 
            echo "Please attach a game file and try again.";
            return;
        }
        
        // Create games dir if it does not exist
        if(!is_dir("resources/games"))
            mkdir("resources/games");

        $sourcePath = "resources/games/" . str_replace(' ', '', $_POST['game-title']);
        mkdir($sourcePath);
        $sourcePath .= "/";

        $platforms = array();

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
        ", $userID, $forumID, $_POST['game-title'], $_POST['game-description'],
        $_POST['game-version'], $now, $now, 0, 0, null);

        $gameID = $this->db->lastInsertID();

        // Insert genres
        if(isset($_POST['game-genres'])) {
            $genres = $_POST['game-genres'];
            for ($i=0; $i < sizeof($genres); $i++) { 
                $this->db->query("INSERT INTO game_genre VALUES ( ? , ? )", $gameID, $genres[$i]);
            }
        }

        // Insert platforms
        for ($i=0; $i < sizeof($platforms); $i++) { 
            $this->db->query("INSERT INTO game_platform VALUES ( ? , ? )", $gameID, $platforms[$i]);
        }

        // Also auto redirect possible
        echo "<h3>Game upload succesful!</h3><a class='btn btn-primary' href='index.php?action=viewGame&id=$gameID'>View Game</a>";
    }

    
}

GameService::$instance = new GameService(Database::$instance);