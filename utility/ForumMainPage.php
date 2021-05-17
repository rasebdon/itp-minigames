<?php
if (isset($_GET['action'])) {
    if ($_GET['action'] == 'forum' && !isset($_GET['id'])) {
        //get an array of all games with forums   
        $games = GameService::$instance->getAllGames();

        //display a banner for each game with link to game forum 
        foreach($games as $game){            
            ?>
            <div class="forum-banner md-12 mb-3 d-flex justify-content-between" onclick="location.href='index.php?action=forum&id=<?= GameService::$instance->getForumID($game)?>';">
                <h1 class="m-0">
                    <span class="d-inline-block"><?= $game->getTitle() ?></span>
                    <span class="d-inline-block text-muted forum-banner-author">by
                        <?= $game->getAuthor()->getUsername(); ?>
                    </span>                    
                </h1>
                <h4 class="m-0"><span class="d-inline-block d-none d-md-block">
                    Posts:
                    <?= ForumService::$instance->getNumberOfPosts(GameService::$instance->getForumID($game))?>
                </span></h2>
            </div>
        
            <?php
        }
        
    }else{
        
        require_once "utility/ForumRenderer.php";

    }
}

?>


