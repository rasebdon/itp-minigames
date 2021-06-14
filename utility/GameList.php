<?php
class GameList
{

    /** @var GameList */
    public static $instance;

    function __construct()
    {
        // Route GET variables
        if (!isset($_GET['action']))
            return;
        switch ($_GET['action']) {
            case "listCreatedGames":
                $this->listCreatedGames();
                break;
        }
    }

    /**
     * Shows all (public, private) games that are currently in development or deployed from a user
     */
    public function listCreatedGames()
    {
        // Get the games of the user
        $games = GameService::$instance->getGamesFromUser($_SESSION['UserID']);
?>
        <section class="game-list">
            <div class="heading-primary">
                <h1 class="heading-primary__text">Creator Dashboard</h1>
            </div>
            <a href="index.php?action=uploadGameInterface" class="button button--primary game-list__upload">
                <span>Upload game</span><i class="fas fa-cloud-upload-alt"></i>
            </a>
            <?php
            if (sizeof($games) == 0) {
            ?>
                <div class="text-center col-12">
                    Pretty empty here! Time to develop some games!
                </div>
            <?php
                return;
            }
            ?>
            <div class="created-games">
                <?php
                for ($i = 0; $i < sizeof($games); $i++) {
                ?>
                    <div class="created-game">
                        <?php
                        $game = $games[$i];

                        // Todo -> Thumbnail
                        $thumbnail = "resources/images/placeholder/placeholder_thumb.jpg";
                        if (sizeof($screenshots = $game->getScreenshots()) > 0)
                            $thumbnail = $screenshots[0];
                        ?>
                        <div class="row" id="created-game-<?= $game->getId() ?>">
                            <div class="created-game__image col-12 col-lg-5">
                                <img src="<?= $thumbnail ?>" width="100%">
                            </div>
                            <div class="created-game__info col-12 col-lg-6 offset-lg-1 row">
                                <a class="no-hyperlink" href="index.php?action=viewGame&id=<?= $game->getId() ?>">
                                    <h2 class="heading-secondary"><?= $game->getTitle() ?></h3>
                                </a>

                                <?= $game->isVerified() == 0 ? "<span class='unverified'>Not verified</span>" : "" ?>

                                <hr>
                                <div class="rating">
                                    <?php
                                    for ($j = 0; $j < 5; $j++) {
                                        $ratingStar = '<span class="fa fa-star';
                                        if ($j < (int)$game->getRating())
                                            $ratingStar .= ' checked';
                                        $ratingStar .= '"></span>';
                                        echo $ratingStar;
                                    }
                                    ?>
                                    <span class="rating"><?php printf("%.2f/5", $game->getRating()); ?></span>
                                </div>
                                <div class="created-game__actions">
                                    <a class="button button--primary" href="index.php?action=editGameInterface&id=<?= $game->getId() ?>">Edit</a>
                                    <a class="button button--primary" href="index.php?action=editGame&id=<?= $game->getId() ?>&deleteGame=1">Delete</a>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php
                }
                ?>
            </div>
        </section>
<?php

    }
}

GameList::$instance = new GameList();
