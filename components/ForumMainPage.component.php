<?php
class ForumMainPageComponent
{
    /** @var ForumMainPageComponent */
    public static $instance;
    function __construct()
    {

        if (isset($_GET['action'])) {
            if ($_GET['action'] == 'forum' && !isset($_GET['id'])) {
                //get an array of all games with forums   
                $games = GameService::$instance->getAllGames();



                //display a banner for each game with link to game forum 
?>
                <div class="heading-primary">
                    <h1 class="heading-primary__text">Forum</h1>
                </div>
                <div class="forum-list">
                    <?php
                    if (isset($_SESSION['UserID']) &&  $_SESSION['UserID'] != null) {
                        if (UserService::$instance->getUser($_SESSION['UserID'])->getUserType()->getAccessStrength() >= UserType::Creator()->getAccessStrength()) {
                    ?>
                            <div class="forum forum-banner md-12 mb-3  justify-content-between" onclick="location.href='index.php?action=forum&id=1';">
                                <h2 class="heading-secondary center">Developer Forum</h2>
                            </div>
                    <?php
                        }
                    }
                    ?>
                    <?php

                    if ($games == null) {
                    ?>
                        <h1 class="text-center mt-5">There are no forums yet! Check back later.</h1>
                    <?php
                        return;
                    }
                    foreach ($games as $game) {
                    ?>
                        <div class="forum forum-banner md-12 mb-3 justify-content-between" onclick="location.href='index.php?action=forum&id=<?= GameService::$instance->getForumID($game) ?>';">

                            <div class="row">

                                <div class="col-12 col-lg-8">
                                    <h2 class="heading-secondary d-inline-block"><?= $game->getTitle() ?></h2>
                                    <span class="d-inline-block text-muted forum-banner-author">by
                                        <?= $game->getAuthor()->getUsername(); ?>
                                    </span>
                                </div>

                                <div class="col-12 col-lg-4 right">
                                    <span class="d-inline-block">
                                        Posts:
                                        <?= ForumService::$instance->getNumberOfPosts(GameService::$instance->getForumID($game)) ?>
                                    </span>
                                </div>
                            </div>

                            <div>
                                <img class="forum-banner-image" src="<?= $game->getFirstScreenshot() ?>" alt="game screenshot">
                            </div>
                        </div>
    <?php
                    }
                    echo "</div>";
                } else {
                    require_once "components/ForumRenderer.component.php";
                }
            }
        }
    }
    // INIT COMPONENT
    ForumMainPageComponent::$instance = new ForumMainPageComponent();
