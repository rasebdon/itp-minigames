<?php
/**
 * User storage class for fast access to often used variables
 */
class User {
    private $id;
    private $username;
    private $firstname;
    private $lastname;
    /** Associative string array with keys: `twitter`, `facebook`, `patreon`, `instagram` */
    private $socialMedia;

    function __construct(int $id, string $username, string $firstname, string $lastname, array $socialMedia)
    {
        $this->id = $id;
        $this->username = $username;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->socialMedia = $socialMedia;
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

    /**
     * @return string|false Returns the link if it is set and false if it is not set
     */ 
    public function getTwitter()
    {
        if(isset($this->socialMedia['twitter']) && $this->socialMedia['twitter'] != "")
            return $this->socialMedia['twitter'];
        return false;
    }
    /**
     * @return string|false Returns the link if it is set and false if it is not set
     */ 
    public function getPatreon()
    {
        if(isset($this->socialMedia['patreon']) && $this->socialMedia['patreon'] != "")
            return $this->socialMedia['patreon'];
        return false;
    }
    /**
     * @return string|false Returns the link if it is set and false if it is not set
     */ 
    public function getInstagram()
    {
        if(isset($this->socialMedia['instagram']) && $this->socialMedia['instagram'] != "")
            return $this->socialMedia['instagram'];
        return false;
    }
    /**
     * @return string|false Returns the link if it is set and false if it is not set
     */ 
    public function getFacebook()
    {
        if(isset($this->socialMedia['facebook']) && $this->socialMedia['facebook'] != "")
            return $this->socialMedia['facebook'];
        return false;
    }
}