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
    public function getPosts(int  $forumid){
        
        $this->db->query("SELECT * FROM post WHERE FK_ForumID = ?", $forumid);
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

        foreach($posts as $post){
            $post->setUpvotes($this->getUpvotesFromPost($post->getId()));
        }

        return $posts;

    }

    //get number of upvotes a specivic post has
    public function getUpvotesFromPost(int $postid){

        $this->db->query("SELECT COUNT(*) FROM vote_post WHERE FK_PostID = ? AND Vote = 1", $postid);

        return count($this->db->fetchAll());

    }
}

ForumService::$instance = new ForumService(Database::$instance);