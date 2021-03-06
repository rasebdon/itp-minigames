<?php
class FrontPageComponent
{
    /** @var FrontPageComponent */
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
    ?>
        <!--Script for game Rating -->

        <script>
            $(document).ready(function() {
                var options = {
                    max_value: 5,
                    step_size: 0.5,
                    selected_symbol_type: 'fontawesome_star',
                    readonly: true,
                }
                $(".rate").rate(options);
            });
        </script>
        <section class="front-page">
            <div class="heading-primary">
                <h1 class="heading-primary__text">
                    <?= isset($_GET['action']) && $_GET['action'] == "favorites" ? "Favorites" : "ITP-Minigames" ?>
                </h1>
            </div>

            <?php
            if ($games == null) {
            ?>
                <div class="text-box text-box--1">
                    <p class="center"><?= isset($_GET['action']) && $_GET['action'] == "favorites" ? "Try adding a game to your favorites!" : "No games found... Come back later!" ?></p>
                </div>
            <?php
                echo "</section>";
                return;
            }
            ?>

            <div class="row">
                <?php
                foreach ($games as $game) {
                ?>
                    <div class="col-12 col-md-6 col-xxl-4 game-card-container">
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
                                        <div class="rate" data-rate-value=<?= $game->getRating() ?>></div>                                        
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
                                                <img src="resources/images/placeholder/placeholder_big.jpg" class="carousel-img" alt="image_<?= $i ?>">
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
                                <a href="index.php?action=viewGame&id=<?= $game->getId() ?>" class="button button--primary">
                                    <span>Visit Page</span><i class="fa fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                        <script>
                            // add script for each carousel, that stops it from riding when flipped over
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
        </section>
<?php
    }
}

FrontPageComponent::$instance = new FrontPageComponent();
