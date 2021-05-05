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
            $platforms = $this->getPlatforms($gamesData[$i]['GameID']);

            $games[$i] = new Game(
                $gamesData[$i]['GameID'],
                $gamesData[$i]['Name'],
                $user,
                $gamesData[$i]['Description'],
                $platforms,
                $gamesData[$i]['Version'],
                $gamesData[$i]['Rating'] == null ? 0 : $gamesData[$i]['Rating'],
                array(), // TODO !
                $gamesData[$i]['PlayCount']
            );
        }
        return $games;
    }

    function getGame($gameID) {
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

        return new Game(
            $gameData['GameID'],
            $gameData['Name'],
            $user,
            $gameData['Description'],
            $platforms,
            $gameData['Version'],
            $gameData['Rating'] == null ? 0 : $gameData['Rating'],
            array(), // TODO !
            $gameData['PlayCount']
        );
    }
  
  //function with dummy data to test stuff
    public function getAllGames(){
        $this->db->query("SELECT * from game");

        // Null reference catch
        if (!($gameData = $this->db->fetchAll()))
            return null;

        
        $gameObjs = array();

        for ($i = 0; $i < sizeof($gameData); $i++) {
            $gameObjs[$i] = new Game(

                $gameData[$i]['GameID'],
                $gameData[$i]['Name'],
                UserService::$instance->getUser($gameData[$i]['FK_UserID']),
                $gameData[$i]['Description'],
                array(),
                $gameData[$i]['Version'],
                0,
                array(), 
                0
            );
        }
        return $gameObjs; 
    }

    public function getGameByForumId(int $forumid) {   
        $this->db->query("SELECT * from game WHERE FK_ForumID = ?", $forumid);

        if (!($gameData = $this->db->fetchAll()))
            return null;

        $gameObj = new Game(

            $gameData[0]['GameID'],
            $gameData[0]['Name'],
            UserService::$instance->getUser($gameData[0]['FK_UserID']),
            $gameData[0]['Description'],
            array(),
            $gameData[0]['Version'],
            0,
            array(), 
            0
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
        $uploadOk = 1;
        $mimeType = mime_content_type($file["tmp_name"]);

        // Check if file already exists
        if (file_exists($target_file)) {
            echo "Sorry, game version already exists.";
            $uploadOk = 0;
        }

        // Check file size
        if ($file["size"] > 500000) {
            echo "Sorry, your game is too large.";
            $uploadOk = 0;
        }

        // Allow certain file formats
        if($mimeType != 'application/zip' && $mimeType != 'application/x-rar-compressed') {
            echo "Sorry, only zip or rar files are allowed.";
            $uploadOk = 0;
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            echo "Sorry, your game was not uploaded.";
        // if everything is ok, try to upload file
        } else if (!move_uploaded_file($file["tmp_name"], $target_file)) {
            echo "Sorry, there was an error uploading your file.";
            exit();
        }
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

        $sourcePath = "resources/games/" . str_replace(' ', '', $_POST['game-title']);
        mkdir($sourcePath);
        $sourcePath .= "/";

        $platforms = array();

        if(isset($windowsFile) && $windowsFile != null && $windowsFile['error'] == 0)
            $this->uploadGameFile($windowsFile, $sourcePath, $_POST['game-version'], Platform::Windows());
            $platforms[sizeof($platforms)] = Platform::Windows()->id;
        if(isset($linuxFile) && $linuxFile != null && $linuxFile['error'] == 0)
            $this->uploadGameFile($linuxFile, $sourcePath, $_POST['game-version'], Platform::Linux());
            $platforms[sizeof($platforms)] = Platform::Linux()->id;
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