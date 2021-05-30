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


        $result = 0; 
        $this->db->query("SELECT COUNT(*) FROM vote_post WHERE FK_PostID = ? AND Vote = 1", $postid); 
 
        $result = $this->db->fetchAll()[0]['COUNT(*)']; 
 
        $this->db->query("SELECT COUNT(*) FROM vote_post WHERE FK_PostID = ? AND Vote = 0", $postid); 
 
        $result -= $this->db->fetchAll()[0]['COUNT(*)']; 
        return $result; 
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

    public function isPostRated($postid, $userid, $rating){ 
 
        $this->db->query( 
            "SELECT * from vote_post WHERE FK_UserID = ? AND FK_PostID = ? AND Vote = ?", $userid, $postid, $rating 
        ); 
        $data = $this->db->fetchAll(); 
        if(empty($data)){           
            return false; 
        }else{ 
            return true; 
        } 
         
    } 
 
    public function toggleLike($postid, $userid){ 
        if(ForumService::$instance->isPostRated($postid, $userid, 1)){ 
            $this->db->query( 
                "DELETE FROM vote_post WHERE FK_UserID = ? AND FK_PostID = ?", $userid, $postid 
            ); 
        }else{ 
            $this->db->query( 
                "REPLACE INTO vote_post (FK_UserID, FK_PostID, Vote) VALUES (?, ?, 1)", $userid, $postid 
            ); 
        } 
        
    } 
 
    public function toggleDislike($postid, $userid){ 
        if(ForumService::$instance->isPostRated($postid, $userid, 0)){ 
            $this->db->query( 
                "DELETE FROM vote_post WHERE FK_UserID = ? AND FK_PostID = ?", $userid, $postid 
            ); 
        }else{ 
            $this->db->query( 
                "REPLACE INTO vote_post (FK_UserID, FK_PostID, Vote) VALUES (?, ?, 0)", $userid, $postid 
            ); 
        } 
 
    } 
 
    public function deletePost($postid){ 
        $this->db->query( 
            "DELETE FROM post WHERE PostID =?", $postid 
        ); 
    } 
 
    public function addPost(Post $post, $forumid){ 
        $this->db->query( 
            "INSERT INTO post ( Title, FK_ForumID, FK_UserID, Text, Date) 
                VALUES (?,?,?,?, ?)", //SQL Statement  
 
            $post->getTitle(), 
            $forumid, 
            $post->getUser()->getId(), 
            $post->getText(), 
            $post->getDate() 
        ); 
 
    } 




    public function insertComment($comment)
    {
        $this->db->query(
            "INSERT INTO comment ( Text, FK_PostID, FK_UserID, Date)
                VALUES (?,?,?,?)", //SQL Statement 

            $comment->getText(),
            $comment->getPost(),
            $comment->getAuthor()->getId(),
            $comment->getDate()
        );
    }

    public function getComments($postid)
    {

        $this->db->query("SELECT * from comment WHERE FK_PostID= ?", $postid);


        if (!($commentData = $this->db->fetchAll()))
            return null;
        //var_dump($commentData);

        foreach ($commentData as $key => $comment) {
            $commentData[$key] = new Comment(

                $comment['CommentID'],
                $comment['Text'],
                ForumService::$instance->getUpvotesFromPost($comment['FK_PostID']),
                UserService::$instance->getUser($comment['FK_UserID']),
                $comment['Date'],
                ForumService::$instance->getUpvotesFromComment($comment['CommentID'])
            );
        }

        return $commentData;
    }


    public function deleteComment($commentID)
    {
        $this->db->query(
            "DELETE FROM comment WHERE CommentID = ?",
            $commentID
        );
    }

    //////////// LIKES/DISLIKES - COMMENT ////////////

    public function getUpvotesFromComment(int $commentid)
    {
        $result = 0;
        $this->db->query("SELECT COUNT(*) FROM vote_comment WHERE FK_CommentID = ? AND Vote = 1", $commentid);

        $result = $this->db->fetchAll()[0]['COUNT(*)'];

        $this->db->query("SELECT COUNT(*) FROM vote_comment WHERE FK_CommentID = ? AND Vote = 0", $commentid);

        $result -= $this->db->fetchAll()[0]['COUNT(*)'];
        return $result;
    }

    public function isCommentRated($commentid, $userid, $rating)
    {

        $this->db->query(
            "SELECT * from vote_comment WHERE FK_UserID = ? AND FK_CommentID = ? AND Vote = ?",
            $userid,
            $commentid,
            $rating
        );
        $data = $this->db->fetchAll();
        if (empty($data)) {
            return false;
        } else {
            return true;
        }
    }

    public function toggleLikeComment($commentid, $userid)
    {
        if (ForumService::$instance->isCommentRated($commentid, $userid, 1)) {
            $this->db->query(
                "DELETE FROM vote_comment WHERE FK_UserID = ? AND FK_CommentID = ?",
                $userid,
                $commentid
            );
        } else {
            $this->db->query(
                "REPLACE INTO vote_comment (FK_UserID, FK_CommentID, Vote) VALUES (?, ?, 1)",
                $userid,
                $commentid
            );
        }
    }

    public function toggleDislikeComment($commentid, $userid)
    {
        if (ForumService::$instance->isCommentRated($commentid, $userid, 0)) {
            $this->db->query(
                "DELETE FROM vote_comment WHERE FK_UserID = ? AND FK_CommentID = ?",
                $userid,
                $commentid
            );
        } else {
            $this->db->query(
                "REPLACE INTO vote_comment (FK_UserID, FK_CommentID, Vote) VALUES (?, ?, 0)",
                $userid,
                $commentid
            );
        }
    }
}

ForumService::$instance = new ForumService(Database::$instance);
