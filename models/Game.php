<?php
/**
 * Game storage class for fast access to often used variables
 */
class Game {
    private $id;
    private $name;
    private $author;
    private $description;
    /** Associative bool array with keys: `windows`, `linux`, `mac` */
    private $plattforms;
    private $version;
    private $rating;

    function __construct(int $id, string $name, User $author, string $description, array $plattforms, string $version, float $rating)
    {
        $this->id = $id;
        $this->name = $name;
        $this->author = $author;
        $this->description = $description;
        $this->plattforms = $plattforms;
        $this->version = $version;
        $this->rating = $rating;
    }

    /**
     * Get the value of id
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the value of name
     */ 
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the value of author
     */ 
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Get the value of description
     */ 
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return true If the game has a windows version
     */ 
    public function hasWindows()
    {
        return isset($this->plattforms['windows']) && $this->plattforms['windows'] === true;
    }

    /**
     * @return true If the game has a linux version
     */ 
    public function hasLinux()
    {
        return isset($this->plattforms['linux']) && $this->plattforms['linux'] === true;
    }

    /**
     * @return true If the game has a mac version
     */ 
    public function hasMac()
    {
        return isset($this->plattforms['mac']) && $this->plattforms['mac'] === true;
    }

    /**
     * Get the value of version
     */ 
    public function getVersion()
    {
            return $this->version;
    }

    /**
     * Get the value of rating
     */ 
    public function getRating()
    {
        return $this->rating;
    }
}