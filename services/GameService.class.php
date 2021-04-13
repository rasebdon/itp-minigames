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
                array(), // TODO !
                $gamesData[$i]['PlayCount']
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
                array()
            );
        }
        return $gameObjs; 
    }

    public function getGameByForumId(int $forumid)
    {   
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
            array()
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

}

GameService::$instance = new GameService(Database::$instance);