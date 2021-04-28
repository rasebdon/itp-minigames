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
    private $playCount;
    /** Array with all screenshot paths */
    private $screenshots;

    function __construct(int $id, string $name, User $author, string $description, array $plattforms, string $version, float $rating, array $screenshots, int $playCount)
    {
        $this->id = $id;
        $this->name = $name;
        $this->author = $author;
        $this->description = $description;
        $this->plattforms = $plattforms;
        $this->version = $version;
        $this->rating = $rating;
        $this->screenshots = $screenshots;
        $this->playCount = $playCount;
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
        return isset($this->plattforms['Windows']) && $this->plattforms['Windows'] === true;
    }

    /**
     * @return true If the game has a linux version
     */ 
    public function hasLinux()
    {
        return isset($this->plattforms['Linux']) && $this->plattforms['Linux'] === true;
    }

    /**
     * @return true If the game has a mac version
     */ 
    public function hasMac()
    {
        return isset($this->plattforms['Mac']) && $this->plattforms['Mac'] === true;
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

    /**
     * Get the value of screenshots
     */ 
    public function getScreenshots()
    {
        return $this->screenshots;
    }

    /**
     * Get the path of the first screenshot
     */
    public function getFirstScreenshot()
    {
        if($this->screenshots == null){
            return "./resources/images/placeholder/placeholder_thumb.jpg";
        }else{
            return $this->screenshots[0];
        }
    }
    /**
     * Get the value of playCount
     */ 
    public function getPlayCount()
    {
        return $this->playCount;
    }
}