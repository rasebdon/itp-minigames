<?php
/**
 * Post storage class for fast access to often used variables
 */
class Post {
    
    private $id;
    private $title;
    private $user;
    private $text;    
    private $date;    
    private $upvotes;

    function __construct(int $id, string $title, User $user, string $text, string $date, int $upvotes)
    {
        $this->id = $id;
        $this->title = $title;
        $this->user = $user;
        $this->text = $text;
        $this->date = $date;  
        $this->upvotes = $upvotes;
    }




    /**
     * Get the value of title
     */ 
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set the value of title
     *
     * @return  self
     */ 
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get the value of user
     */ 
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set the value of user
     *
     * @return  self
     */ 
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get the value of text
     */ 
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set the value of text
     *
     * @return  self
     */ 
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get the value of date
     */ 
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set the value of date
     *
     * @return  self
     */ 
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get the value of upvotes
     */ 
    public function getUpvotes()
    {
        return $this->upvotes;
    }

    /**
     * Get the value of id
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */ 
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Set the value of upvotes
     *
     * @return  self
     */ 
    public function setUpvotes($upvotes)
    {
        $this->upvotes = $upvotes;

        return $this;
    }
}