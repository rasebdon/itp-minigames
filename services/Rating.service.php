<?php
//handle rating concerning trafic to the database 
class RatingService
{
    public static $instance;
    protected $db;
    function __construct(Database $database)
    {
        $this->db = $database;
    }

    function getRatings(int $gameid, string $sort = "new"){

        switch ($sort) {
            case "new":
                $sortsql = " ORDER BY Date ";
                break;
            default:
                $sortsql = " ORDER BY Date ";
                break;
        }

        $this->db->query("SELECT * FROM rating WHERE FK_GameID = ? ", $gameid);
        $rating_array = $this->db->fetchAll();
        $ratings = array();
        foreach ($rating_array as $rating) {
            $ratings[] = new Rating(
                UserService::$instance->getUser($rating['FK_UserID']),
                $rating['FK_GameID'],
                $rating['Text'],
                $rating['Date'],
                $rating['Rating']
            );
        }
        return $ratings;
    }

    function insertRating(Rating $rating){

        $this->db->query( 
            "REPLACE INTO rating ( FK_UserID, FK_GameID, Text, Date, Rating) 
                VALUES (?,?,?,?,?)", //SQL Statement  
 
            $rating->getUser()->getId(), 
            $rating->getGameid(), 
            $rating->getText(), 
            $rating->getDate(), 
            $rating->getRating() 
        );
        return true;
    }

    function getRating(int $gameid, int $userid){

        $this->db->query("SELECT * FROM rating WHERE FK_GameID = ? AND FK_UserID = ? ", $gameid, $userid);

        if (!($ratingData = $this->db->fetchArray()))
            return false;

        $rating = new Rating(
            UserService::$instance->getUser($ratingData['FK_UserID']),
            $ratingData['FK_GameID'],
            $ratingData['Text'],
            $ratingData['Date'],
            $ratingData['Rating']
        );        
        return $rating;
    }

    
}

RatingService::$instance = new RatingService(Database::$instance);