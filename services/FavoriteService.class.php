<?php
class FavoriteService {
    
    /** @var FavoriteService  */
    public static $instance;
    /** @var Database  */
    protected $db;

    function __construct(Database $database)
    {
        $this->db = $database;
    }
    

    public function insertFavorite($gameid, $userid){

        $this->db->query(
            "INSERT INTO  favorite 
            (FK_UserID, FK_GameID)
            VALUES (?, ?)",
            $userid,
            $gameid
        );
    }

    public function removeFavorite($gameid, $userid){
        
        $this->db->query("DELETE FROM favorite WHERE FK_UserID = ? AND FK_GameID = ?", $userid, $gameid);
    }

    public function getFavorites(){
        $result = $this->db->query(
            "SELECT * from favorite WHERE FK_UserID = ? AND FK_GameID = ?", $_SESSION["UserID"], $_GET["id"]
        );
        return $result;
    }

    public function isFavorite($gameid, $userid){
        $this->db->query(
            "SELECT * from favorite WHERE FK_UserID = ? AND FK_GameID = ?", $userid, $gameid
        );
        if (!($data = $this->db->fetchArray()))return false;
        return true;
    }

    public function getFavorites(){
        $result = $this->db->query(
            "SELECT * from favorite WHERE FK_UserID = ? AND FK_GameID = ?", $_SESSION["User_ID"], $_GET["id"]
        );
        return $result;
    }    
}
// INIT SERVICE

FavoriteService::$instance = new FavoriteService(Database::$instance);


