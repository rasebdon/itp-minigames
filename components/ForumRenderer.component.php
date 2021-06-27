<?php

/**
 * Static class that holds forum rendering functions
 */
class ForumRendererComponent
{

    /** @var ForumRendererComponent */
    public static $instance;

    function __construct()
    {
        if (!isset($_GET['action']) || !isset($_GET['id']))
            return;

        switch ($_GET['action']) {
            case "forum":
                if (isset($_POST['TogglePostLike'])) {
                    ForumService::$instance->toggleLike($_POST['TogglePostLike'], $_SESSION['UserID']);
                }
                if (isset($_POST['TogglePostDislike'])) {
                    ForumService::$instance->toggleDislike($_POST['TogglePostDislike'], $_SESSION['UserID']);
                }
                if (isset($_POST['DeletePost'])) {
                    ForumService::$instance->deletePost($_POST['DeletePost']);
                }
                if (isset($_POST['postPost']) && !empty($_POST['PostText']) && !empty($_POST['PostTitle'])) {
                    // var_dump($_POST['PostText']);
                    if (isset($_SESSION['PostError']))
                        unset($_SESSION['PostError']);

                    $post = new Post(
                        0,
                        $_POST['PostTitle'],
                        UserService::$instance->getUser($_SESSION['UserID']),
                        $_POST['PostText'],
                        date("Y-m-d H:i:s"),
                        0
                    );
                    ForumService::$instance->addPost($post, $_GET['id']);
                } else if (isset($_POST['postPost'])) {
                    $_SESSION['PostError']['textEmpty'] = "<div class='mt-1 alert alert-danger' role='alert'> Can't be empty </div>";
                }
                if ($_GET['id'] == 1) {
                    $this->RenderForumDeveloper();
                } else {
                    $this->RenderForum($_GET['id']);
                }
                break;
        }
    }

    function RenderForum(int $forumid)
    {
        $posts = ForumService::$instance->getPosts($forumid);
        $game = GameService::$instance->getGameByForumId($forumid);
        $screenshots = $game->getScreenshots();
        if (sizeof($screenshots) == 0) {
            $banner = "resources/images/placeholder/placeholder_big.jpg";
        } else {
            $banner = $screenshots[0];
        }
?>
        <div class="img-wrap col-4" style="width:100%; height: 400px; background: url(<?= $game->getFirstScreenshot() ?>);
    background-repeat: no-repeat;
    background-position: center center;
    background-size: cover;"></div>

        <div class="forum-header col-md-6 px-0">
            <h1 class="heading-secondary"><?= $game->getTitle() ?></h1>
            <p class="mb-0 forum-linktogame">
                <a href="index.php?action=viewGame&id=<?= $game->getId() ?>" class="button button--primary" style="margin-bottom: 15px;">View Game</a>
            </p>
        </div>



        <?php
        if (isset($_SESSION['UserID']) &&  $_SESSION['UserID'] != null) {
            $this->displayPostForm($forumid);
        }

        ?>

        <form id="TogglePostLike" action="index.php?action=forum&id=<?= $forumid ?>" method="POST"></form>
        <form id="TogglePostDislike" action="index.php?action=forum&id=<?= $forumid ?>" method="POST"></form>
        <form id="DeletePost" action="index.php?action=forum&id=<?= $forumid ?>" method="POST"></form>


        <div class="forum-list">
            <?php
            foreach ($posts as $post) {
            ?>
                <div class="forum post-banner md-12 mb-3 border-bottom rounded" id="<?= $post->getId() ?>" onclick="location.href='index.php?action=post&id=<?= $post->getId() ?>';">
                    <div class="col-10 bl-1">
                        <span class="h1 d-block "><?= $post->getTitle() ?>
                            <?php
                            if (isset($_SESSION['UserID']) &&  $_SESSION['UserID'] != null) {
                                if (UserService::$instance->getUser($_SESSION['UserID'])->getUserType()->getAccessStrength() >= UserType::Admin()->getAccessStrength()) {
                            ?>
                                    <button type='submit' class='btn' value='<?= $post->getId() ?>' form='DeletePost' name='DeletePost'>
                                        <i class="fas fa-trash deleteButton"></i>
                                    </button>
                            <?php
                                }
                            }
                            ?>
                        </span>
                        <span class="d-block text-muted post-text-preview">
                            <?= $post->getText(); ?>
                        </span>
                    </div>
                    <div class="d-inline-block upvote-display col-1">
                        <?php
                        if (isset($_SESSION['UserID']) &&  $_SESSION['UserID'] != null) {
                            if (ForumService::$instance->isPostRated($post->getId(), $_SESSION['UserID'], 1)) {
                        ?>
                                <form action="index.php?action=forum&id=<?= $forumid ?>#<?= $post->getId() ?>" method="POST">
                                    <button type='submit' class='btn' value='<?= $post->getId() ?>' name='TogglePostLike'>
                                        <i class="fas fa-plus likeButtonVoted"></i>
                                    </button>
                                </form>
                            <?php
                            } else {
                            ?>
                                <form action="index.php?action=forum&id=<?= $forumid ?>#<?= $post->getId() ?>" method="POST">
                                    <button type='submit' class='btn' value='<?= $post->getId() ?>' name='TogglePostLike'>
                                        <i class=" fas fa-plus likeButton"></i>
                                    </button>
                                </form>
                        <?php
                            }
                        } else {
                            echo '<i class=" fas fa-plus likeButton"></i>';
                        }
                        ?>
                        <div><?= $post->getUpvotes() ?></div>
                        <?php
                        if (isset($_SESSION['UserID']) &&  $_SESSION['UserID'] != null) {
                            if (ForumService::$instance->isPostRated($post->getId(), $_SESSION['UserID'], 0)) {
                        ?>
                                <form action="index.php?action=forum&id=<?= $forumid ?>#<?= $post->getId() ?>" method="POST">
                                    <button type='submit' class='btn' value='<?= $post->getId() ?>' name='TogglePostDislike'>
                                        <i class="fas fa-minus dislikeButtonVoted"></i>
                                    </button>
                                </form>
                            <?php
                            } else {
                            ?>
                                <form action="index.php?action=forum&id=<?= $forumid ?>#<?= $post->getId() ?>" method="POST">
                                    <button type='submit' class='btn' value='<?= $post->getId() ?>' name='TogglePostDislike'>
                                        <i class="fas fa-minus dislikeButton"></i>
                                    </button>
                                </form>
                        <?php
                            }
                        } else {
                            echo '  <i class="fas fa-minus dislikeButton"></i>';
                        }
                        echo '</div>';

                        //check if have the rights to delete a Post 
                        ?>



                    </div>
                <?php
            }
            //echo "</div>";
        }

        function displayPostForm(int $forumid)
        {
                ?>
                <div class="col-md-12">
                    <h1 style="margin-top: 20px;">Create your own post:</h1>
                    <form id="postPost" action="index.php?action=forum&id=<?= $forumid ?>" method="POST">
                        <input type="text" name="PostTitle" id="PostTitle" class="form__input" placeholder="Your Post needs a title">
                        <textarea name="PostText" id="PostText" class="form__input" placeholder="Text of your post" cols="40" rows="5"></textarea><br>
                        <small><?= $_SESSION['PostError']['textEmpty'] ?? '' ?></small>
                        <div style="text-align:right; margin-bottom: 36px;"><button id="postPost" name="postPost" class="button button--primary d-inline-block">Post</button></div>
                    </form>
                </div>
            <?php
        }

        function RenderForumDeveloper()
        {
            $posts = ForumService::$instance->getPosts(1);
            $banner = "resources/images/placeholder/placeholder_big.jpg";
            ?>
                <!--<div class="p-4 p-md-5 mb-4 rounded forum-center-backround" style="background-image: url('<?= $banner ?>');
                    background-repeat: no-repeat;
                    background-position: center;">
            </div>-->

                <div class="forum-header custom-shadow col-md-6 px-0" style="height:auto">
                    <!--<h1 class="display-4">Developer Forum</h1>-->
                    <h1 class="heading-secondary">Developer Forum</h1>
                </div>

                <?php
                if (isset($_SESSION['UserID']) &&  $_SESSION['UserID'] != null) {
                    $this->displayPostForm(1);
                }

                ?>
                <form id="TogglePostLike" action="index.php?action=forum&id=1" method="POST"></form>
                <form id="TogglePostDislike" action="index.php?action=forum&id=1" method="POST"></form>
                <form id="DeletePost" action="index.php?action=forum&id=1" method="POST"></form>

                <div class="forum-list">
                    <?php
                    foreach ($posts as $post) {
                    ?>
                        <div class="forum post-banner md-12 mb-3 rounded" id="<?= $post->getId() ?>" onclick="location.href='index.php?action=post&id=<?= $post->getId() ?>';">
                            <div class="col-10 bl-1">
                                <span class="h1 d-block "><?= $post->getTitle() ?>
                                    <?php
                                    if (isset($_SESSION['UserID']) &&  $_SESSION['UserID'] != null) {
                                        if (UserService::$instance->getUser($_SESSION['UserID'])->getUserType()->getAccessStrength() >= UserType::Admin()->getAccessStrength()) {
                                    ?>
                                            <button type='submit' class='btn' value='<?= $post->getId() ?>' form='DeletePost' name='DeletePost'>
                                                <i class="fas fa-trash deleteButton"></i>
                                            </button>
                                    <?php
                                        }
                                    }
                                    ?>
                                </span>
                                <span class="d-block text-muted post-text-preview" style="line-height: 2.1em;">
                                    <?= $post->getText(); ?>
                                </span>
                            </div>
                            <div class="d-inline-block upvote-display col-1">
                                <?php
                                if (isset($_SESSION['UserID']) &&  $_SESSION['UserID'] != null) {
                                    if (ForumService::$instance->isPostRated($post->getId(), $_SESSION['UserID'], 1)) {
                                ?>
                                        <form action="index.php?action=forum&id=1#<?= $post->getId() ?>" method="POST">
                                            <button type='submit' class='btn' value='<?= $post->getId() ?>' name='TogglePostLike'>
                                                <i class="fas fa-plus likeButtonVoted"></i>
                                            </button>
                                        </form>
                                    <?php
                                    } else {
                                    ?>
                                        <form action="index.php?action=forum&id=1#<?= $post->getId() ?>" method="POST">
                                            <button type='submit' class='btn' value='<?= $post->getId() ?>' name='TogglePostLike'>
                                                <i class="fas fa-plus likeButton"></i>
                                            </button>
                                        </form>
                                <?php
                                    }
                                } else {
                                    echo '<i class="fa fa-arrow-circle-up" style="color:black"></i>';
                                }
                                ?>
                                <div><?= $post->getUpvotes() ?></div>
                                <?php
                                if (isset($_SESSION['UserID']) &&  $_SESSION['UserID'] != null) {
                                    if (ForumService::$instance->isPostRated($post->getId(), $_SESSION['UserID'], 0)) {
                                ?>
                                        <form action="index.php?action=forum&id=1#<?= $post->getId() ?>" method="POST">
                                            <button type='submit' class='btn' value='<?= $post->getId() ?>' name='TogglePostDislike'>
                                                <i class="fas fa-minus dislikeButtonVoted"></i>
                                            </button>
                                        </form>
                                    <?php
                                    } else {
                                    ?>
                                        <form action="index.php?action=forum&id=1#<?= $post->getId() ?>" method="POST">
                                            <button type='submit' class='btn' value='<?= $post->getId() ?>' name='TogglePostDislike'>
                                                <i class="fas fa-minus dislikeButton"></i>
                                            </button>
                                        </form>
                                <?php
                                    }
                                } else {
                                    echo '<i class="fa fa-arrow-circle-down" style="color:black"></i>';
                                }
                                echo '</div>';

                                //check if have the rights to delete a Post 
                                ?>



                            </div>
                <?php
                    }
                    echo "</div>";
                }
            }

            ForumRendererComponent::$instance = new ForumRendererComponent();
