<?php
class FrontPage
{
    /** @var FrontPage */
    public static $instance;

    function __construct()
    {
        if (isset($_GET['search'])) {
            $this->displayFrontPage(
                GameService::$instance->searchGames($_GET['search'])
            );
        } else if (isset($_GET['action']) && $_GET['action'] == "favorites" && isset($_SESSION['UserID'])) {
            $this->displayFrontPage(
                GameService::$instance->getFavorites($_SESSION['UserID'])
            );
        } else if (!isset($_GET['action'])) {
            $this->displayFrontPage(
                GameService::$instance->getAllGames()
            );
        }
    }


    function displayFrontPage($games)
    {
        if ($games != null) {
            //--HTML--
?>
            <section class="front-page">

                <!-- <div class="text-box--1 gradient-secondary">
    <h1 class="heading-primary__main">ITP-Minigames</h1>
</div> -->
              <h1 class="headerfrontPage text-center shadow p-3 mb-5 bg-body rounded text-primary">
                <?= isset($_GET['action']) && $_GET['action'] == "favorites" ? "Favorites" : "ITP-Minigames" ?>
            </h1>
                <!--Note: These will be several rows with several Items -->
                <div class="row">
                    <?php
                    foreach ($games as $game) {
                    ?>
                        <div class="col-12 col-md-6 col-xxl-4">
                            <div class="game-card" id="gameCard<?= $game->getId() ?>">
                                <div class="game-card__side game-card__side--front gradient-primary">
                                    <div class="game-card__image">
                                        <img class="game-card__image--default-content" src="<?= $game->getFirstScreenshot() ?>" alt="image">
                                    </div>
                                    <div class="game-card__info">
                                        <h4 class="game-card__title">
                                            <?= $game->getTitle() ?>
                                        </h4>
                                        <h5 class="game-card__developer">
                                            <?= $game->getAuthor()->getUsername() ?>
                                        </h5>
                                        <div class="game-card__platforms">
                                            <?php
                                            if ($game->hasWindows()) echo '<i class="platform-icon fa fa-windows fa-lg" aria-hidden="true"></i>';
                                            if ($game->hasLinux()) echo '<i class="platform-icon fa fa-linux fa-lg" aria-hidden="true"></i>';
                                            if ($game->hasMac()) echo '<i class="platform-icon fa fa-apple fa-lg" aria-hidden="true"></i>';
                                            ?>
                                        </div>
                                        <div class="game-card__rating">
                                            <?php
                                            for ($i = 0; $i < 5; $i++) {
                                                echo '<i class="fa fa-star';
                                                if ($i < (int)$game->getRating())
                                                    echo ' checked';
                                                echo '"></i>';
                                            }
                                            ?>
                                        </div>
                                        <div class="game-card__genres">
                                            <?php

                                            if (($genres = GameService::$instance->getGameGenres($game->getID())) != null) {
                                                echo '<ul>';
                                                foreach ($genres as $genre) {
                                                    echo '<li class="genre">' . $genre . '</li>';
                                                }
                                                echo '</ul>';
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="game-card__side game-card__side--back gradient-primary--reverse">
                                    <div id="carouselFrontPage<?= $game->getId() ?>" data-bs-ride="false" class="carousel slide carousel-fade game-card__carousel" data-bs-ride="carousel">
                                        <div class="carousel-inner">
                                        <?php
                                            $screenshots = $game->getScreenshots();

                                            // Add carousel items with screenshots as src
                                            for ($i = 0; $i < sizeof($screenshots); $i++) {
                                            ?>
                                                <div class="carousel-item<?= $i === 0 ? " active" : "" ?>">
                                                    <img src="<?= $screenshots[$i] ?>" class="carousel-img" alt="screenshot<?= $i ?>">
                                                </div>
                                            <?php
                                            }
                                            if (sizeof($screenshots) == 0) {
                                            ?>
                                                <div class="carousel-item<?= $i === 0 ? " active" : "" ?>">
                                                    <img src="resources/images/placeholder/placeholder_thumb.jpg" class="carousel-img" alt="image_<?= $i ?>">
                                                </div>
                                            <?php
                                            }
                                            ?>
                                        </div>
                                        <button class="carousel-control-prev" type="button" data-bs-target="#carouselFrontPage<?= $game->getId() ?>" data-bs-slide="prev">
                                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                            <span class="visually-hidden">Previous</span>
                                        </button>
                                        <button class="carousel-control-next" type="button" data-bs-target="#carouselFrontPage<?= $game->getId() ?>" data-bs-slide="next">
                                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                            <span class="visually-hidden">Next</span>
                                        </button>
                                    </div>
                                    <a href="index.php?action=viewGame&id=<?= $game->getId() ?>" class="button button--primary"><span>Visit Page</span><i class="fa fa-arrow-right" aria-hidden="true"></i> </a>
                                </div>
                            </div>
                            <script>
                                const gameCard<?= $game->getId() ?> = document.querySelector("#gameCard<?= $game->getId() ?>");
                                const thisCarousel<?= $game->getId() ?> = document.querySelector("#carouselFrontPage<?= $game->getId() ?>");
                                const carouselId<?= $game->getId() ?> = new bootstrap.Carousel(thisCarousel<?= $game->getId() ?>, {
                                    interval: 2500
                                })
                                gameCard<?= $game->getId() ?>.addEventListener("mouseover", function(e) {
                                    carouselId<?= $game->getId() ?>.cycle();
                                })
                                gameCard<?= $game->getId() ?>.addEventListener("mouseout", function(e) {
                                    carouselId<?= $game->getId() ?>.pause();
                                })
                            </script>
                        </div>
                    <?php
                    }
                    ?>
                </div>
            <?php
        } else {
            ?>
                <h1 class="headerfrontPage text-center shadow p-3 mb-5 bg-body rounded text-primary">No Games Found</h1>

            <?php
        }
            ?>
            </section>
    <?php
    }
}

FrontPage::$instance = new FrontPage();
