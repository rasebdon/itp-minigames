<?php
/**
 * Rating storage class for fast access to often used variables
 */
class Rating {
    
    private $user;
    private $gameid;
    private $text;    
    private $date;    
    private $rating;

    function __construct(User $user, int $gameid, $text, string $date, int $rating)
    {
        $this->user = $user;
        $this->gameid = $gameid;
        $this->text = $text;
        $this->date = $date;  
        $this->rating = $rating;
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
         * Get the value of gameid
         */ 
        public function getGameid()
        {
                return $this->gameid;
        }

        /**
         * Set the value of gameid
         *
         * @return  self
         */ 
        public function setGameid($gameid)
        {
                $this->gameid = $gameid;

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
         * Get the value of rating
         */ 
        public function getRating()
        {
                return $this->rating;
        }

        /**
         * Set the value of rating
         *
         * @return  self
         */ 
        public function setRating($rating)
        {
                $this->rating = $rating;

                return $this;
        }
}