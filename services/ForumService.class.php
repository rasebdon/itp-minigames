<?php
//handle forum concerning trafic to the database 
class ForumService
{
    public static $instance;
    protected $db;
    function __construct(Database $database)
    {
        $this->db = $database;
    }

    //get the posts belonging to a forum
    public function getPosts(int  $forumid, string $sort = "new")
    {

        switch ($sort) {
            case "new":
                $sortsql = " ORDER BY Date ";
                break;
            default:
                $sortsql = " ORDER BY Date ";
                break;
        }

        $this->db->query("SELECT * FROM post WHERE FK_ForumID = ?" . $sortsql, $forumid);
        $posts_array = $this->db->fetchAll();
        $posts = array();
        foreach ($posts_array as $post) {
            $posts[] = new Post(
                $post['PostID'],
                $post['Title'],
                UserService::$instance->getUser($post['FK_UserID']),
                $post['Text'],
                $post['Date'],
                0
            );
        }

        foreach ($posts as $post) {
            $post->setUpvotes($this->getUpvotesFromPost($post->getId()));
        }

        return $posts;
    }

    //get number of upvotes a specivic post has
    public function getUpvotesFromPost(int $postid)
    {

        $this->db->query("SELECT COUNT(*) FROM vote_post WHERE FK_PostID = ? AND Vote = 1", $postid);

        return $this->db->fetchAll()[0]['COUNT(*)'];
    }

    public function getNumberOfPosts(int $forumid)
    {

        $this->db->query("SELECT COUNT(*) FROM post WHERE FK_ForumID = ? ", $forumid);
        return $this->db->fetchAll()[0]['COUNT(*)'];
    }

    //show one posts
    function getPost($postid)
    {
        $this->db->query("SELECT * from post WHERE PostID = ?", $postid);


        if (!($postData = $this->db->fetchArray()))
            return null;

        $postObj = new Post(

            $postData['PostID'],
            $postData['Title'],
            UserService::$instance->getUser($postData['FK_UserID']),
            $postData['Text'],
            $postData['Date'],
            ForumService::$instance->getUpvotesFromPost($postData['PostID']),
        );

        //$postid->setUpvotes($this->getUpvotesFromPost($postid->getId()));

        return $postObj;
    }
}

ForumService::$instance = new ForumService(Database::$instance);
