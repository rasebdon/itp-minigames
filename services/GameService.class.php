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

}

GameService::$instance = new GameService(Database::$instance);