<?php

class TicketService{

    /** @var TicketService  */
    public static $instance;
    /** @var Database  */
    protected $db;

    function __construct(Database $database)
    {
        $this->db = $database;
    }

    function getTicketFromData($ticketData)
    {
        // Null reference catch
        if ($ticketData == null)
            return null;

        $user = UserService::$instance->getUser($ticketData["FK_UserID"]);
        $userEmail = UserService::$instance->getUserEmail($ticketData["FK_UserID"]);
    
        return new Ticket($ticketData["TicketID"],$user, $ticketData["Subject"], $ticketData["Text"], $userEmail);
    }

    public function ticketsToArray($array){
        // Null reference catch
        if ($array == null || sizeof($array) == 0)
            return array();

        $ticketObjs = array();

        for ($i = 0; $i < sizeof($array); $i++) {
            $ticketObjs[$i] = TicketService::$instance->getTicketFromData($array[$i]);
        }
        
        return $ticketObjs;
    }

    public function getAllTickets(){
        
        $query = "SELECT * FROM `tickets` ORDER BY TicketID ASC";
        $this->db->query($query);
        $ticketData = $this->db->fetchAll();
        return $this->ticketsToArray($ticketData);
    }

    public function deleteTicket($ticketID){
        $query = "DELETE FROM `tickets` WHERE `tickets`.`TicketID`=?";
        $this->db->query($query, $ticketID);
    }
}

TicketService::$instance = new TicketService(Database::$instance);