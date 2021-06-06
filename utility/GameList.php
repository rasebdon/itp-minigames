<?php
class GameList {
    
    /** @var GameList */
    public static $instance;

    function __construct()
    {
        // Route GET variables
        if(!isset($_GET['action']))
            return;
        switch($_GET['action']) {
            case "listCreatedGames":
                $this->listCreatedGames();
                break;
        }
    }

    /**
     * Shows all (public, private) games that are currently in developement or deployed from a user
     */
    public function listCreatedGames() {
        // Get the games of the user
        $games = GameService::$instance->getGamesFromUser($_SESSION['UserID']);

        ?>
        <div id="created-games-list" class="row">
            <div class="col-12 h1 text-center mb-5">Creator Dashboard</div>
            <a href="index.php?action=uploadGameInterface" class="btn btn-success mb-3">Upload game</a>
        <?php

        if(sizeof($games) == 0) {
            ?>
            <div class="text-center col-12">
                Pretty empty here! Time to develop some games!
            </div>
            <?php
            return;
        }

        for ($i = 0; $i < sizeof($games); $i++) { 
            $game = $games[$i];

            // Todo -> Thumbnail
            $thumbnail = "resources/images/placeholder/placeholder_thumb.jpg";
            if(sizeof($screenshots = $game->getScreenshots()) > 0)
                $thumbnail = $screenshots[0];
            ?>
            <div class="created-game col-12 row mb-3" id="created-game-<?=$game->getId()?>">
                <div class="col-3"><img src="<?=$thumbnail?>" width="100%"></div>
                <div class="col-9 row">
                    <a class="no-hyperlink" href="index.php?action=viewGame&id=<?=$game->getId()?>"><div class="col-8 h2"><?=$game->getTitle()?></div></a>
                    <div class="col-12">
                    <?php 
                    for ($j=0; $j < 5; $j++) { 
                        echo '<span class="fa fa-star';
                        if($j < (int)$game->getRating())
                            echo ' checked';
                        echo '"></span>';
                    }
                    ?>
                    <span class="rating"><?php printf("%.2f/5", $game->getRating()); ?></span>
                    </div>
                    <div class="col-12">
                        <span class="playcount">Played times: <?= $game->getPlayCount(); ?></span>
                    </div>
                    <div class="col-12">
                        <a class="button button--primary" href="index.php?action=editGameInterface&id=<?=$game->getId()?>">Edit</a>
                        <!-- <a class="btn btn-primary" href="index.php?action=viewGameDetails&id=<?=$game->getId()?>">Analytics</a> -->
                    </div>
                </div>
            </div>
            <?php
        }

        ?>
        </div>
        <?php

    }
}

GameList::$instance = new GameList();