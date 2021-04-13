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
            (SELECT AVG(Rating) FROM rating WHERE GameID = g1.GameID) as Rating
            FROM game g1
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
                $gamesData[$i]['Rating'],
                array() // TODO !
            );
        }
        return $games;
    }

    function getGame($gameID) {
        $this->db->query(
            "SELECT *,
            (SELECT AVG(Rating) FROM rating WHERE GameID = g1.GameID) as Rating
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
            $gameData['Rating'],
            array() // TODO !
        );
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
}

GameService::$instance = new GameService(Database::$instance);