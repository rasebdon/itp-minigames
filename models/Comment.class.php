<?php

class Comment
{
    private $CommentID;
    private $Text;
    private $FK_PostID;
    private $FK_UserID;
    private $Date;


    public function __construct($CommentID, $Text, $FK_PostID, $FK_UserID, $Date)
    {
        $this->CommentID = $CommentID;
        $this->Text = $Text;
        $this->FK_PostID = $FK_PostID;
        $this->FK_UserID = $FK_UserID;
        $this->Date = $Date;
    }

    /**
     * Get the value of id
     */
    public function getId()
    {
        return $this->CommentID;
    }

    /**
     * Get the value of author
     */
    public function getAuthor()
    {
        return $this->FK_UserID;
    }

    /**
     * Get the value of post
     */
    public function getPost()
    {
        return $this->FK_PostID;;
    }

    /**
     * Get the value of date
     */
    public function getDate()
    {
        return $this->Date;
    }

    /**
     * Get the value of text
     */
    public function getText()
    {
        return $this->Text;
    }
}
