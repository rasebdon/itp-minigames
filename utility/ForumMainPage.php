<?php
if (isset($_GET['action'])) {
    if ($_GET['action'] == 'forum') {
        //get an array of all games with forums   
        $games = GameService::getAllGames();        

        foreach($games as $game){
            ?>
            <div class="forum-banner md-12 mb-2">
                <h1 class="m-0">
                    <span class="d-inline-block"><?= $game->getName() ?></span>
                    <span class="d-inline-block game-version">
                        <?= $game->getVersion() ?>
                    </span>
                </h1>
            </div>

            <?php

        }

?>







<?php
    }
}

?>


