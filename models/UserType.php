<?php
class UserType {

    private $typeString;
    private $accessStrength;

    function __construct($type)
    {
        if(preg_match("/(?i)user/", $type)) {
            $this->typeString = "User";
            $this->accessStrength = 1;
        } 
        else if(preg_match("/(?i)admin/", $type)) {
            $this->typeString = "Admin";
            $this->accessStrength = 10;
        } 
        else if(preg_match("/(?i)creator/", $type)) {
            $this->typeString = "Creator";
            $this->accessStrength = 5;
        }
        else {
            $this->typeString = "Unknown";
            $this->accessStrength = 0;
        }
    }

    public static function User() {
        return new UserType("User");
    }
    public static function Admin() {
        return new UserType("Admin");
    }
    public static function Creator() {
        return new UserType("Creator");
    }

    /**
     * Get the value of type
     */ 
    public function getTypeString()
    {
        return $this->typeString;
    }

    /**
     * Get the value of accessStrength
     */ 
    public function getAccessStrength()
    {
        return $this->accessStrength;
    }
}