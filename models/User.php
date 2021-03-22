<?php
/**
 * User storage class for fast access to often used variables
 */
class User {
    private $id;
    private $username;
    private $firstname;
    private $lastname;

    function __construct(int $id, string $username, string $firstname, string $lastname)
    {
        $this->id = $id;
        $this->username = $username;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
    }

    /**
     * Get the value of id
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the value of username
     */ 
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Get the value of firstname
     */ 
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Get the value of lastname
     */ 
    public function getLastname()
    {
        return $this->lastname;
    }
}