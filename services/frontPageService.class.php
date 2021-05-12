<?php

class FrontPageService
{
    /** @var FrontPageService  */
    public static $instance;
    /** @var Database  */
    protected $db;

    function __construct(Database $database)
    {
        $this->db = $database;
    }

    public function getGenreName($genreID)
    {
        $this->db->query("SELECT `Name` FROM `genre` WHERE `GenreID` = ?;", $genreID);
        $result = $this->db->fetchArray();
        return $result['Name'];
        
    }

    public function getGenresToGame($gameID)
    {
        $result = array();
        $key = 0;
        $this->db->query("SELECT `FK_GenreID` FROM `game_genre` 
                          WHERE `FK_GameID` = ?;", $gameID);
        $genreIDs = $this->db->fetchAll();
        foreach($genreIDs as $genreID){
           $result[$key] = $this->getGenreName($genreID);
           $key++;
        }
        return $result;
    }
}

FrontPageService::$instance = new FrontPageService(Database::$instance);
