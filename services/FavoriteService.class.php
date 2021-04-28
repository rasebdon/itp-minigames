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
    
    public function insertFavoriteGame(){
        $this->db->query(
            "INSERT INTO  favorite 
            (FK_UserID, FK_GameID)
            VALUES (?, ?)",
            $_SESSION["User_ID"],
            $_GET["id"]
        );
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


