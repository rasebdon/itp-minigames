<?php
/**
 * Ticket storage class for fast access to often used variables
 */
class Ticket {
    private $ticketID;
    private $user;
    private $title;
    private $text;
    private $userEmail;

    function __construct($ticketID, $user, $title, $text, $userEmail)
    {
        $this->ticketID = $ticketID;
        $this->user = $user;
        $this->title = $title;
        $this->text = $text;
        $this->userEmail = $userEmail;
    }

    public function getUser(){
        return $this->user;
    }

    public function getTitle(){
        return $this->title;
    }

    public function getText(){
        return $this->text;
    }

    public function getTicketID(){
        return $this->ticketID;
    }

    public function getEmail(){
        return $this->userEmail;
    }
}

