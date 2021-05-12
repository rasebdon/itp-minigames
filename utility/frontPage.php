<?php
if (!isset($_GET['action'])) {

    if (isset($_GET['search'])) {
        $games = GameService::$instance->searchGames($_GET['search']);

    } else if (!isset($_GET['action'])) {
        $games = GameService::$instance->getAllGames();
    }
    if ($games != null) {
        //--HTML--
?>
        <h1 class="headerfrontPage text-center shadow p-3 mb-5 bg-body rounded text-primary">ITP-Minigames</h1>
        <div class="container border gamePreviewContainer">
            <!--Note: These will be several rows with several Items -->
            <div class="row gamePreviewRow">

                <?php

                foreach ($games as $game) {
                    $genres = FrontPageService::$instance->getGenresToGame($game->getID());
                    /*To-Do :
                    #TAG's
                    #Link to Author
                    */
                    echo '<div class="col-3 border game-display mt-2 mb-2" >
                    <div class="sm-3 mb-2">
                        <h5 class="m-0">

                            <a href="index.php?action=viewGame&id=' . $game->getId() . '" class="d-inline-block">' . $game->getTitle() . '</a>

                            <span class="d-inline-block game-version">
                                ' . $game->getVersion() . '
                            </span>
                        </h5>
                    </div>
                    <div class="row">    
                        <div class="col-6">
                            
                            <span class="author">Author: ' . $game->getAuthor()->getUsername() . '</span>
                        </div>
                        <div class="col-6 pb-1">
                        ';

                    for ($i = 0; $i < 5; $i++) {
                        echo '<span class="fa fa-star';
                        if ($i < (int)$game->getRating())
                            echo ' checked';
                        echo '"></span>';
                    }
                    echo '<span class="rating">';
                    printf("%.2f/5", $game->getRating());
                    echo '</span>
                        </div>
                    </div>
                    <div class="col-12 border-top border-bottom pb-3 pt-3 screenshots">
                        <!-- SCREENSHOTS -->
                        <div class="col-12  mt-1 mb-1 ">
                            <div class="thumbnail-container">
                                

                                <img src="' . $game->getFirstScreenshot() . '" alt="Game Preview not loaded" class="img-fluid max-width: 100%">

                            
                            </div>
                        </div>
                    </div>
                    <div>
                        <span class="genre">
                            Genre:';
                            if($genres != null){
                                foreach($genres as &$genre){
                                    echo '<span> '.$genre.',</span>';
                                    
                                }
                            }else{
                                echo '<span>No Genres added</span>';
                            }
                        echo'
                        </span>
                    </div>
                </div>';
                }
                unset($game);
                ?>
            </div>
        <?php
    } else {
        ?>
            <h1 class="headerfrontPage text-center shadow p-3 mb-5 bg-body rounded text-primary">No Games Found</h1>
            
    <?php
    }
}


