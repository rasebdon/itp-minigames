<?php
class ContactService {
    
    /** @var ContactService  */
    public static $instance;
    /** @var Database  */
    protected $db;

    function __construct(Database $database)
    {
        $this->db = $database;
    }

    function addTicket($text, $userID, $subject)
    {
        $this->db->query(
            "INSERT INTO  tickets
            (FK_UserID, Text, Subject)
            VALUES (?, ?, ?)",
            $userID,
            $text,
            $subject
        );
    }



}

//Init Service
ContactService::$instance = new ContactService(Database::$instance);