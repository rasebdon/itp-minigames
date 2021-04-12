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
        $game = GameService::$instance->getGameByForumId($forumid);
        ?>
        <div class="p-4 p-md-5 mb-4 rounded forum-center-backround" 
            style="background-image: url('https://news.xbox.com/de-de/wp-content/uploads/sites/3/2020/04/Minecraft-RTX-Beta_Hero.jpg?fit=1920%2C1080');
                    background-repeat: no-repeat;
                    background-position: center;">
        <div class="forum-header custom-shadow col-md-6 px-0">
            <h1 class="display-4"><?= $game->getName()?></h1>  
            <p class="mb-0 forum-linktogame">
                <a href="index.php?action=viewGame&id=<?= $game->getId()?>" class="custom-shadow">visit site...</a>
            </p>   
        </div>
        </div>

        <?php
        foreach($posts as $post){            
            ?>
            <div class="post-banner md-12 mb-3 border-bottom rounded" onclick="location.href='index.php?action=post&id=<?= $post->getId?>';">
                <div class="col-10 bl-2">
                    <span class="h1 d-block "><?= $post->getTitle() ?></span>
                    <span class="d-block text-muted post-text-preview">
                        <?= $post->getText(); ?>
                    </span>                    
                </div>
                <div class="d-inline-block upvote-display col-1 offset-1">
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
