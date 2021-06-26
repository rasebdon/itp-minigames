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



                if (isset($_SESSION['UserID']) &&  $_SESSION['UserID'] != null) {
                    if (UserService::$instance->getUser($_SESSION['UserID'])->getUserType()->getAccessStrength() >= UserType::Creator()->getAccessStrength()) {
?>

                        <div onclick="location.href='index.php?action=forum&id=1';" class="heading-primary">

                            <h1 class="heading-primary__text">Developer Forum</h1>


                            <!--<h1 class="m-0">
                                <span class="d-inline-block">Developer Forum</span>
                                <span class="d-inline-block text-muted forum-banner-author"> </span>
                            </h1>
                            <h4 class="m-0"><span class="d-inline-block d-none d-md-block">
                                    Posts:
                                    <?= ForumService::$instance->getNumberOfPosts(1) ?>
                                </span></h2>-->
                        </div>
                <?php
                    }
                }


                //display a banner for each game with link to game forum 
                ?>
                <div class="heading-primary">
                    <h1 class="heading-primary__text">Forum</h1>
                </div>

                <div class="forum-list">
                    <?php

                    if ($games == null) {
                    ?>
                        <h1 class="text-center mt-5">There are no forums yet! Check back later.</h1>
                    <?php
                        return;
                    }
                    foreach ($games as $game) {
                    ?>


                        <div class="forum forum-banner md-12 mb-3 row justify-content-between" onclick="location.href='index.php?action=forum&id=<?= GameService::$instance->getForumID($game) ?>';">
                            <h1 class="m-0 col-11 " style="line-break: anywhere;">
                                <span class="d-inline-block"><?= $game->getTitle() ?></span>
                                <span class="d-inline-block text-muted forum-banner-author">by
                                    <?= $game->getAuthor()->getUsername(); ?>
                                </span>
                            </h1>

                            <h4 class="m-0 col-1"><span class="d-inline-block d-none d-md-block">
                                    Posts:
                                    <?= ForumService::$instance->getNumberOfPosts(GameService::$instance->getForumID($game)) ?>
                                </span></h4>

                            <div class="img-wrap col-4" style="width:100%; height: 200px; background: url(<?= $game->getFirstScreenshot() ?>);
    background-repeat: no-repeat;
    background-position: center center;
    background-size: cover;"></div>
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
