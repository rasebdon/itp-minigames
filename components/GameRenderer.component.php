<?php

/**
 * Class that holds game rendering functions
 */
class GameRendererComponent
{

    /** @var GameRendererComponent */
    public static $instance;

    function __construct()
    {
        if (!isset($_GET['action']))
            return;

        switch ($_GET['action']) {
            case "viewGame":
                $game = GameService::$instance->getGame($_GET['id']);
                if (
                    $game != null &&
                    ($game->isVerified() ||
                        (isset($_SESSION['AccessStrength']) && $_SESSION['AccessStrength'] >= UserType::Admin()->getAccessStrength() ||
                            (isset($_SESSION['UserID']) && $game->getAuthor()->getId() == $_SESSION['UserID'])))
                )
                    $this->RenderGame($game);
                break;
            case "rateGame":
                // THIS NOW RESIDES IN /forms/formUserUpdate.php, can't send headers after they are already sent.
                //var_dump($_POST['rateGame']);
                // if (isset($_POST['rateGame']) && isset($_SESSION['UserID'])) {
                //     if (RatingService::$instance->insertRating(new Rating(UserService::$instance->getUser($_SESSION['UserID']), $_GET['id'], $_POST['rating-text'], date("Y-m-d H:i:s"), $_POST['rating-value']))) {
                //         //do something for error handeling
                //     }
                // }
                // header('Location: index.php?action=viewGame&id=' . $_GET['id']);
                break;
        }
    }

    /**
     * Renders game as HTML
     * 
     * @param Game|null $game
     */
    function RenderGame($game = null)
    {
        // Do not render if function is called without game
        if ($game == null) {
            return;
        }
?>
        <section class="game-page">
            <div class="row">
                <div class="col-12 col-md-8">
                    <h1 class="heading-primary__text left">
                        <?= $game->getTitle() ?>
                    </h1>
                    <?php
                    if ($game->getVersion() != null) {
                    ?>
                        <span class="version">
                            Version <?= $game->getVersion() ?>
                        </span>
                    <?php
                    }
                    if ($game->isVerified() == 0) {
                    ?>
                        <span class="unverified">
                            Not verified
                        </span>
                    <?php
                    }
                    ?>
                </div>
                <div class="col-12 col-md-4 right">
                    <span class="author">Creator: <?= $game->getAuthor()->getUsername(); ?></span>
                    <div class="game-page__rate">
                        <script>
                            $(document).ready(function() {
                                var options = {
                                    max_value: 5,
                                    step_size: 0.5,
                                    selected_symbol_type: 'fontawesome_star',
                                    url: 'http://localhost/itproject/itp-minigames/services/ratingService.php',
                                    readonly: true,
                                }
                                $(".rate").rate(options);
                            });
                        </script>
                        <div class="rate" data-rate-value=<?= $game->getRating() ?>>

                        </div>
                    </div>
                    <span class="rating"><?php printf("%.2f/5", $game->getRating()); ?></span>

                </div>
            </div>
            <div class="flex flex__between flex__wrap">
                <!-- Favorite Button-->
                <?php
                if (isset($_SESSION['UserID']) &&  $_SESSION['UserID'] != null) {
                    if (FavoriteService::$instance->isFavorite($_GET['id'],  $_SESSION['UserID'])) {
                ?>
                        <form action="index.php?action=viewGame&id=<?= $_GET['id'] ?>" method="POST">
                            <button type="submit" class="button button--primary mt-3" value="<?= $_GET['id'] ?>" name="removeFavorite">Remove from Favorites</button>
                        </form>
                    <?php
                    } else {
                    ?>
                        <form action="index.php?action=viewGame&id=<?= $_GET['id'] ?>" method="POST">
                            <button type="submit" class="button button--primary mt-3" value="<?= $_GET['id'] ?>" name="addFavorite">Add to Favorites</button>
                        </form>
                    <?php
                    }
                    // Check for rights
                    $user = UserService::$instance->getUser($_SESSION['UserID']);
                    if (
                        $_SESSION['UserID'] == $game->getAuthor()->getId() ||
                        $user->getUserType()->getAccessStrength() == UserType::Admin()->getAccessStrength()
                    ) {
                    ?>
                        <!-- Delete Button -->
                        <a href="index.php?action=editGame&id=<?= $game->getId() ?>&deleteGame=true" type="button" class="button button--primary mt-3">Delete</a>
                <?php
                    }
                }
                ?>
            </div>
            <?php
            $screenshots = $game->getScreenshots();
            if ($screenshots != null) {
            ?>
                <div class="game-page__screenshots">
                    <!-- SCREENSHOTS -->
                    <div id="gamePageScreenshots" data-bs-ride="carousel" class="carousel slide carousel-fade game-page__carousel">
                        <div class="carousel-inner">
                            <?php
                            // Add carousel items with screenshots as src
                            for ($i = 0; $i < sizeof($screenshots); $i++) {
                            ?>
                                <div class="carousel-item<?= $i === 0 ? " active" : "" ?>">
                                    <img src="<?= $screenshots[$i] ?>" class="carousel-img" alt="screenshot<?= $i ?>">
                                </div>
                            <?php
                            }
                            ?>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#gamePageScreenshots" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#gamePageScreenshots" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div>
                </div>
            <?php
            }
            ?>
            <div class="game-page__downloads">
                <h2 class="heading-secondary">Downloads</h2>
                <?php
                if ($game->hasWindows()) {
                ?>
                    <div class="row mb-3">
                        <div class="col-4 d-flex align-content-center flex-wrap">
                            Windows
                        </div>
                        <div class="col-6 text-end">
                            <a class="button button--primary" href="<?= "resources/games/" . urlencode(str_replace(' ', '', $game->getTitle())) . "/" . $game->getVersion() . "_" . Platform::Windows()->name . ".zip" ?>">
                                Download
                            </a>
                        </div>
                    </div>
                <?php
                }
                if ($game->hasMac()) {
                ?>
                    <div class="row mb-3">
                        <div class="col-4 d-flex align-content-center flex-wrap">
                            Mac
                        </div>
                        <div class="col-6 text-end">
                            <a class="button button--primary" href="<?= "resources/games/" . urlencode(str_replace(' ', '', $game->getTitle())) . "/" . $game->getVersion() . "_" . Platform::Mac()->name . ".zip" ?>">
                                Download
                            </a>
                        </div>
                    </div>
                <?php
                }
                if ($game->hasLinux()) {
                ?>
                    <div class="row mb-3">
                        <div class="col-4 d-flex align-content-center flex-wrap">
                            Linux
                        </div>
                        <div class="col-6 text-end">
                            <a class="button button--primary" href="<?= "resources/games/" . urlencode(str_replace(' ', '', $game->getTitle())) . "/" . $game->getVersion() . "_" . Platform::Linux()->name . ".zip" ?>">
                                Download
                            </a>
                        </div>
                    </div>
                <?php
                }
                ?>
            </div>
            <div class="game-page__description">
                <div class="heading-secondary">
                    Description
                </div>
                <p>
                    <?= $game->getDescription() ?>
                </p>
                <?php
                if (($genres = GameService::$instance->getGameGenres($game->getID())) != null) {
                    echo '<ul class="mt-5 mb-0 p-0">';
                    foreach ($genres as $genre) {
                        echo '<li class="genre">' . $genre . '</li>';
                    }
                    echo '</ul>';
                }
                ?>
            </div>
            <div class="row border game-display mt-5 mb-5">
                <div class="d-flex flex-column col-2 text-center">
                    <!-- SOCIAL MEDIA LINKS -->
                    <?php
                    // Twitter 
                    if ($link = $game->getAuthor()->getTwitter()) {
                    ?>
                        <a href="<?= $link ?>" target="_blank" rel="noopener noreferrer">
                            <svg class="twitter" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-twitter" viewBox="0 0 16 16">
                                <path d="M5.026 15c6.038 0 9.341-5.003 9.341-9.334 0-.14 0-.282-.006-.422A6.685 6.685 0 0 0 16 3.542a6.658 6.658 0 0 1-1.889.518 3.301 3.301 0 0 0 1.447-1.817 6.533 6.533 0 0 1-2.087.793A3.286 3.286 0 0 0 7.875 6.03a9.325 9.325 0 0 1-6.767-3.429 3.289 3.289 0 0 0 1.018 4.382A3.323 3.323 0 0 1 .64 6.575v.045a3.288 3.288 0 0 0 2.632 3.218 3.203 3.203 0 0 1-.865.115 3.23 3.23 0 0 1-.614-.057 3.283 3.283 0 0 0 3.067 2.277A6.588 6.588 0 0 1 .78 13.58a6.32 6.32 0 0 1-.78-.045A9.344 9.344 0 0 0 5.026 15z" />
                            </svg>
                        </a>
                    <?php
                    }
                    // Patreon 
                    if ($link = $game->getAuthor()->getPatreon()) {
                    ?>
                        <a href="<?= $link ?>" target="_blank" rel="noopener noreferrer">
                            <svg class="patreon" height="64" viewBox="0 0 32 32" width="64" xmlns="http://www.w3.org/2000/svg">
                                <path d="m206 0c11.1 2 22.4 3.3 33.3 6 75.2 18.6 132.2 82.4 142.8 159.4 14.1 102.4-52.5 196-154 216.3-7.3 1.5-14.8 2.3-22.2 3.4h-102l.1-193.4c0-4.3.4-8.7 1.1-12.9 6.6-43.3 46.1-76 89.4-74.3 45.7 1.9 82.4 37.2 85.3 82.2 4.3 66.8-63.7 113.7-124.6 85.9-1.3-.6-2.6-1.1-4.5-1.8l.2 60.1c0 1.2 2.3 3 3.8 3.4 19.5 5.6 39.4 6.7 59.4 3.5 84.6-13.6 139.2-93.9 120.9-177.3-16.9-77.2-94.1-127.7-171-112-69.6 14-118.2 73.4-118.4 144.6v186.9c0 1.7.2 3.3.4 5h-46v-207l1.8-11.8c10.6-77.7 67.3-141.3 143.4-160.3 10.8-2.7 21.9-4 32.8-5.9z" fill="#e6461a" />
                                <path d="m1680 272h-31.7v-22.9l-.3-88.5c-.3-23.1-17.7-43.9-40.3-48.6-31-6.5-62.5 18.6-62.6 50.4l.2 109.7h-30.8v-183.2h30.5v10.1c10-8.9 20.6-14.4 32.6-17.4 47.2-11.9 96.6 22.8 101.4 71.2.1 1.4.6 2.9.9 4.3zm-1198.2-20.5v91.4h-30.6l-.3-3.9.1-159.5c.3-48.1 34-88.7 81-98.2 56.5-11.4 111.8 28.3 119.2 85.5 7.3 56.4-31.5 107-88.1 113.8-29.7 3.5-55.3-6.7-77.3-26.5-1-.9-1.5-2.5-2.3-3.8-.6.4-1.1.8-1.7 1.2zm69.9-1.1c38.7-.1 69.6-31.3 69.6-70.1-.1-38.7-31.4-70-69.9-69.8-38.6.2-69.6 31.4-69.5 70.2-.1 38.6 31.1 69.8 69.8 69.7zm576-29.1c14.8 22.6 46.1 33.8 73.8 26.4 16.9-4.5 30.4-14.2 40.1-28.7s13.3-30.7 11.2-48.4h30.6c5.5 38.7-17.6 89.2-69.4 105.7-49 15.6-101.9-8.4-122.6-55.5-20.8-47.3-2.5-102.8 42.6-128.5 46.7-26.6 99-9.2 123.4 19.9zm-13-29.5 91.8-77.2c-23.3-9.7-53-3.2-71.9 15.7-16.6 16.7-22.9 37-19.9 61.5zm-250.7 80.3h-30.9v-18.9l-3.3 2.7c-57.3 49.6-145.6 22-164.2-51.3-17.1-67.6 38.5-131.9 107.8-124.9 51 5.2 90 47.7 90.5 98.9zm-170.5-92.1c-.1 38.4 31 69.9 69.2 70.1 38.6.1 70.3-30.8 70.4-68.8.1-39.7-30.7-71.1-69.6-71.1-38.6-.2-69.9 31.1-70 69.8zm601.8 0c.1-55.6 45.3-100.8 100.7-100.8 55.6 0 101 45.7 100.7 101.3-.3 55.7-45.4 100.6-101 100.5-55.4-.1-100.5-45.4-100.4-101zm100.6 70c38.6 0 70-31.2 70-69.8.1-38.5-31.3-70.1-69.7-70.2-38.5-.1-70 31.3-70.1 69.8 0 38.7 31.3 70.2 69.8 70.2zm-522.1-161.7h17.7v-70.8h31v70.8h46.2v30.9h-46.1v152.8h-30.8v-6l.1-141c0-4.5-1.1-6.3-5.8-5.8-3.9.4-7.9.1-12.2.1zm141.7 26.8c14.7-13.2 31.5-22.3 50.9-25.7 6.8-1.2 13.8-1.3 21.2-2v28.4c0 3.5-2.6 2.9-4.6 2.9-15.4.2-29.2 5.1-41.1 14.6-17.3 13.7-26.7 31.7-27 53.9l-.1 84.8h-30.5v-183.3h29.9v25.8c.5.2.9.4 1.3.6z" fill="#222c31" />
                                <path d="m17.164 0 2.767.5c6.25 1.546 10.985 6.847 11.866 13.245 1.173 8.508-4.362 16.285-12.797 17.972-.607.125-1.23.19-1.845.283h-8.475l.008-16.07c0-.357.033-.723.09-1.072.548-3.598 3.83-6.315 7.43-6.174 3.797.158 6.847 3.09 7.088 6.83.357 5.55-5.293 9.448-10.354 7.138-.108-.05-.216-.09-.374-.15l.017 4.994c0 .1.19.25.316.283 1.62.465 3.274.557 4.936.29 7.03-1.13 11.567-7.803 10.046-14.733-1.404-6.415-7.82-10.61-14.21-9.307-5.783 1.163-9.822 6.1-9.838 12.016v15.53c0 .14.017.274.033.415h-3.821v-17.2l.15-.98c.88-6.456 5.591-11.74 11.915-13.31.897-.224 1.82-.332 2.726-.5h2.327z" fill="#e6461a" />
                            </svg>
                        </a>
                    <?php
                    }
                    // Instagram 
                    if ($link = $game->getAuthor()->getInstagram()) {
                    ?>
                        <a href="<?= $link ?>" target="_blank" rel="noopener noreferrer">
                            <svg class="instagram" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-instagram" viewBox="0 0 16 16">
                                <path d="M8 0C5.829 0 5.556.01 4.703.048 3.85.088 3.269.222 2.76.42a3.917 3.917 0 0 0-1.417.923A3.927 3.927 0 0 0 .42 2.76C.222 3.268.087 3.85.048 4.7.01 5.555 0 5.827 0 8.001c0 2.172.01 2.444.048 3.297.04.852.174 1.433.372 1.942.205.526.478.972.923 1.417.444.445.89.719 1.416.923.51.198 1.09.333 1.942.372C5.555 15.99 5.827 16 8 16s2.444-.01 3.298-.048c.851-.04 1.434-.174 1.943-.372a3.916 3.916 0 0 0 1.416-.923c.445-.445.718-.891.923-1.417.197-.509.332-1.09.372-1.942C15.99 10.445 16 10.173 16 8s-.01-2.445-.048-3.299c-.04-.851-.175-1.433-.372-1.941a3.926 3.926 0 0 0-.923-1.417A3.911 3.911 0 0 0 13.24.42c-.51-.198-1.092-.333-1.943-.372C10.443.01 10.172 0 7.998 0h.003zm-.717 1.442h.718c2.136 0 2.389.007 3.232.046.78.035 1.204.166 1.486.275.373.145.64.319.92.599.28.28.453.546.598.92.11.281.24.705.275 1.485.039.843.047 1.096.047 3.231s-.008 2.389-.047 3.232c-.035.78-.166 1.203-.275 1.485a2.47 2.47 0 0 1-.599.919c-.28.28-.546.453-.92.598-.28.11-.704.24-1.485.276-.843.038-1.096.047-3.232.047s-2.39-.009-3.233-.047c-.78-.036-1.203-.166-1.485-.276a2.478 2.478 0 0 1-.92-.598 2.48 2.48 0 0 1-.6-.92c-.109-.281-.24-.705-.275-1.485-.038-.843-.046-1.096-.046-3.233 0-2.136.008-2.388.046-3.231.036-.78.166-1.204.276-1.486.145-.373.319-.64.599-.92.28-.28.546-.453.92-.598.282-.11.705-.24 1.485-.276.738-.034 1.024-.044 2.515-.045v.002zm4.988 1.328a.96.96 0 1 0 0 1.92.96.96 0 0 0 0-1.92zm-4.27 1.122a4.109 4.109 0 1 0 0 8.217 4.109 4.109 0 0 0 0-8.217zm0 1.441a2.667 2.667 0 1 1 0 5.334 2.667 2.667 0 0 1 0-5.334z" />
                            </svg>
                        </a>
                    <?php
                    }
                    // Facebook 
                    if ($link = $game->getAuthor()->getFacebook()) {
                    ?>
                        <a href="<?= $link ?>" target="_blank" rel="noopener noreferrer">
                            <svg class="facebook" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-facebook" viewBox="0 0 16 16">
                                <path d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951z" />
                            </svg>
                        </a>
                    <?php
                    }
                    ?>
                </div>
            </div>
            <?php
            $this->renderRating($game);
            ?>
        </section>
    <?php


    }

    function renderRating($game)
    {
        $rating5 = GameService::$instance->getRatingByStars($game->getId(), 5);
        $rating4 = GameService::$instance->getRatingByStars($game->getId(), 4);
        $rating3 = GameService::$instance->getRatingByStars($game->getId(), 3);
        $rating2 = GameService::$instance->getRatingByStars($game->getId(), 2);
        $rating1 = GameService::$instance->getRatingByStars($game->getId(), 1);
        $rating_total = ($rating5 + $rating4 + $rating3 + $rating2 + $rating1);
        if ($rating_total == 0) $rating_total = 1;

    ?>
        <div class="row rating-display mb-5">
            <div class="col-md-3">
                <h1 class="m-0">Rating</h1>
                <div class="row mb-3">
                    <div class="col">Average</div>
                    <div class="col">
                        <div class="rate" data-rate-value=<?= $game->getRating() ?>></div>
                    </div>
                </div>

                <div>
                    <span>5 Stars</span>
                    <div class="rating-bar-outer">
                        <div class="rating-bar-inner" style="width: <?= $rating5 / $rating_total * 100 ?>%;"></div>
                        <div class="rating-bar-percent"><?= round($rating5 / $rating_total * 100) ?>%</div>
                    </div>
                </div>
                <div>
                    <span>4 Stars</span>
                    <div class="rating-bar-outer">
                        <div class="rating-bar-inner" style="width: <?= $rating4 / $rating_total * 100 ?>%;"></div>
                        <div class="rating-bar-percent"><?= round($rating4 / $rating_total * 100) ?>%</div>
                    </div>
                </div>
                <div>
                    <span>3 Stars</span>
                    <div class="rating-bar-outer">
                        <div class="rating-bar-inner" style="width: <?= $rating3 / $rating_total * 100 ?>%;"></div>
                        <div class="rating-bar-percent"><?= round($rating3 / $rating_total * 100) ?>%</div>
                    </div>
                </div>
                <div>
                    <span>2 Stars</span>
                    <div class="rating-bar-outer">
                        <div class="rating-bar-inner" style="width: <?= $rating2 / $rating_total * 100 ?>%;"></div>
                        <div class="rating-bar-percent"><?= round($rating2 / $rating_total * 100) ?>%</div>
                    </div>
                </div>
                <div>
                    <span>1 Star</span>
                    <div class="rating-bar-outer">
                        <div class="rating-bar-inner" style="width: <?= $rating1 / $rating_total * 100 ?>%;"></div>
                        <div class="rating-bar-percent"><?= round($rating1 / $rating_total * 100) ?>%</div>
                    </div>
                </div>
            </div>
            <div class="col-md-9">
                <h2 class="heading-secondary">
                    Feedback
                </h2>

                <?php
                if (isset($_SESSION['UserID']) &&  $_SESSION['UserID'] != null) {

                    $userRating = RatingService::$instance->getRating($game->getId(), $_SESSION['UserID']);
                ?>
                    <div class="row">


                        <?php
                        if ($userRating) {
                            $oldRating = $userRating->getRating();
                            $oldText = $userRating->getText();
                            echo "<span>Edit own feedback:</span>";
                        } else {
                            $oldRating = 0;
                            $oldText = "";
                            echo "<span>Add own feedback:</span>";
                        }
                        ?>
                        <script>
                            $(document).ready(function() {
                                var options2 = {
                                    max_value: 5,
                                    step_size: 1.0,
                                    initial_value: <?= $oldRating ?>,
                                    selected_symbol_type: 'fontawesome_star',
                                    readonly: false,
                                }
                                $(".rate2").rate(options2);
                            });
                        </script>

                        <div class="col-4">Rating</div>
                        <div class="col">
                            <div class="rate2" data-rate-value=<?= $oldRating ?>></div>
                        </div>
                        <form action="index.php?action=rateGame&id=<?= $game->getId() ?>" method="POST">
                            <div class="form__group ">
                                <textarea name="rating-text" id="rating-text" class="form__input mt-2" cols="30" rows="3" placeholder="Type some feedback"><?= $oldText ?></textarea>
                                <span class="form__separator"></span>
                            </div>
                            <small><?= $_SESSION['ratingError']['valueZero'] ?? '' ?></small>
                            <button class="button button--primary mt-2" name="rateGame">Submit</button>
                            <input type="number" name="rating-value" id="rating-value" value="<?= $oldRating ?>" hidden>
                        </form>
                    </div>
                <?php
                }
                ?>
                <div class="game-page__feedback">

                    <?php
                    $feedbacks = RatingService::$instance->getRatings($game->getId());
                    foreach ($feedbacks as $feedback) {
                    ?>
                        <div class="game-page__feedback-single">
                            <div class="row mt-4">
                                <div class="col-12 col-sm-6">
                                    <img class="profile-picture--contain profile-picture--small" src="<?= ProfilePictureService::$instance->getPicture($feedback->getUser()->getFK_PictureID())->getThumbnailPath() ?>" alt="profilePicture">
                                    <span class="username"><?= $feedback->getUser()->getUsername() ?></span>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="rate" data-rate-value=<?= $feedback->getRating() ?>></div>
                                </div>
                            </div>
                            <?php
                            if ($feedback->getText() != NULL) {
                            ?>
                                <p class="game-page__feedback-single__text"><?= $feedback->getText() ?></p>
                        </div>
                <?php
                            }
                        }
                ?>
                </div>
            </div>
        </div>
<?php
    }
}

GameRendererComponent::$instance = new GameRendererComponent();
