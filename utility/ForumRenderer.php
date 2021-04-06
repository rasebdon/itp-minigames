<?php
/**
 * Static class that holds forum rendering functions
 */
class ForumRenderer {

    /** @var ForumRenderer */
    public static $instance;

    function __construct()
    {
        if(!isset($_GET['action']) || !isset($_GET['id'])) 
            return;
     
        switch($_GET['action']) {
            case "forum":                
                $this->RenderForum($_GET['id']);
                break;
        }
    }
    
    function RenderForum(int $forumid) {

        $posts = ForumService::$instance->getPosts($forumid);
        foreach($posts as $post){            
            ?>
            <div class="forum-banner md-12 mb-3 d-flex justify-content-between" onclick="location.href='index.php?action=post&id=<?= $post->getId?>';">
                <h1 class="m-0">
                    <span class="d-inline-block"><?= $post->getTitle() ?></span>
                    <span class="d-inline-block text-muted forum-banner-author">
                        <?= $post->getText(); ?>
                    </span>                    
                </h1>
                <div class="d-inline-block upvote-display">
                    <i class="fa fa-arrow-circle-up"></i>
                    <div><?= $post->getUpvotes()?></div>
                    <i class="fa fa-arrow-circle-down"></i>
                </div>
            </div>
            
        
            <?php
        }        
    }
}

ForumRenderer::$instance = new ForumRenderer();
