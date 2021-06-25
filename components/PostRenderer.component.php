<?php

/**
 * Class that holds post rendering functions
 */
class PostRendererComponent
{
    /** @var PostRendererComponent */
    public static $instance;

    function __construct()
    {
        if (!isset($_GET['action']) || !isset($_GET['id']))
            return;

        switch ($_GET['action']) {
            case "post":
                $this->RenderPost($_GET['id']);
                break;
        }
    }

    function renderPost($onePost)
    {
        $post = ForumService::$instance->getPost($onePost);

        if (isset($_POST['commentText']) && !empty($_POST['commentText'])) {

            //var_dump($_POST);
            $commentObj = new Comment(
                0,
                $_POST['commentText'],
                $post->getId(),
                UserService::$instance->getUser($_SESSION['UserID']),
                date("Y-m-d H:i:s"),
                0
            );

            ForumService::$instance->insertComment($commentObj);
        }
        //var_dump($post);

        //var_dump($allComment);
        if (isset($_POST['DeleteComment'])) {

            ForumService::$instance->deleteComment($_POST['DeleteComment']);
        }

        if (isset($_POST['ToggleCommentLike'])) {
            echo "test";
            ForumService::$instance->toggleLikeComment($_POST['ToggleCommentLike'], $_SESSION['UserID']);
        }
        if (isset($_POST['ToggleCommentDislike'])) {
            ForumService::$instance->toggleDislikeComment($_POST['ToggleCommentDislike'], $_SESSION['UserID']);
        }
        $allComment = ForumService::$instance->getComments($post->getId());

?>
        <form id="DeleteComment" action="index.php?action=post&id=<?= $post->getId() ?>" method="POST"></form>
        <form id="ToggleCommentLike" action="index.php?action=post&id=<?= $post->getID() ?>" method="POST"></form>
        <form id="ToggleCommentDislike" action="index.php?action=post&id=<?= $post->getId() ?>" method="POST"></form>
        <div id="thePost">
            <h1 class="display-4"><?= $post->getTitle() ?></h1>
            <h2><?= $post->getUser()->getUsername() ?></h2>
            <p><?= $post->getText() ?></p>
        </div>

        <?php
        if (isset($_SESSION['UserID']) &&  $_SESSION['UserID'] != null) {
        ?> <div class="row">
                <div class="col-md-12">
                    <form method="POST">
                        <textarea name="commentText" id="mainComment" class="form-control form__input" placeholder="add comment - be friendly" cols="40" rows="5"></textarea><br>
                        <button class="btn-primary btn" id="addComment">Add comment</button>
                    </form>
                </div>
            </div>
            <?php
        }

        if (!empty($allComment)) {
            if (count($allComment) == 1) {
            ?><h2>1 Comment </h2>
            <?php
            } else if (count($allComment) > 1) {
            ?><h2><?= count($allComment) ?> Comments </h2>
            <?php
            }
        } else {
            ?><h2>No comments added</h2>
            <?php
        }

        if (!empty($allComment)) {
            foreach ($allComment as $comment) {
            ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="userComments">
                            <div class="comment">
                                <div class="user"><?= $comment->getAuthor()->getUsername() ?><span class="time"><?= $comment->getDate() ?></span></div>
                                <div class="userComment"><?= $comment->getText() ?></div>

                                <div>
                                    <div class="col-10 bl-1">
                                        <span class="h1 d-block ">
                                            <?php
                                            //button for admin to delet comment 
                                            if (isset($_SESSION['UserID']) &&  $_SESSION['UserID'] != null) {
                                                if (UserService::$instance->getUser($_SESSION['UserID'])->getUserType()->getAccessStrength() >= UserType::Admin()->getAccessStrength()) {
                                            ?>
                                                    <form>
                                                        <button type='submit' class='btn' value='<?= $comment->getId() ?>' form='DeleteComment' name='DeleteComment'>
                                                            <i class="fa fa-trash" style="color:red"></i>
                                                        </button>
                                                    </form>
                                            <?php
                                                }
                                            }
                                            ?>
                                        </span>
                                        <div>
                                            <?php
                                            //likes
                                            if (isset($_SESSION['UserID']) &&  $_SESSION['UserID'] != null) {
                                                if (ForumService::$instance->isCommentRated($comment->getId(), $_SESSION['UserID'], 1)) {
                                            ?>
                                                    <button type='submit' class='btn' value='<?= $comment->getId() ?>' form='ToggleCommentLike' name='ToggleCommentLike'>
                                                        <i class="fa fa-arrow-circle-up" style="color:blue"></i>
                                                    </button>
                                                <?php
                                                } else {
                                                ?>
                                                    <button type='submit' class='btn' value='<?= $comment->getId() ?>' form='ToggleCommentLike' name='ToggleCommentLike'>
                                                        <i class="fa fa-arrow-circle-up" style="color:black"></i>
                                                    </button>
                                            <?php
                                                }
                                            } else {
                                                echo '<i class="fa fa-arrow-circle-up" style="color:black"></i>';
                                            }
                                            ?>
                                            <!-- Number of Votes -->
                                            <div><?= ForumService::$instance->getUpvotesFromComment($comment->getId()) ?></div>
                                            <?php
                                            //dislikes
                                            if (isset($_SESSION['UserID']) &&  $_SESSION['UserID'] != null) {
                                                if (ForumService::$instance->isCommentRated($comment->getId(), $_SESSION['UserID'], 0)) {
                                            ?>
                                                    <button type='submit' class='btn' value='<?= $comment->getId() ?>' form='ToggleCommentDislike' name='ToggleCommentDislike'>
                                                        <i class="fa fa-arrow-circle-down" style="color:blue"></i>
                                                    </button>
                                                <?php
                                                } else {
                                                ?>
                                                    <button type='submit' class='btn' value='<?= $comment->getId() ?>' form='ToggleCommentDislike' name='ToggleCommentDislike'>
                                                        <i class="fa fa-arrow-circle-down" style="color:black"></i>
                                                    </button>
                                            <?php
                                                }
                                            } else {
                                                echo '<i class="fa fa-arrow-circle-down" style="color:black"></i>';
                                            }
                                            ?>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
    <?php
            }
        }
    }
}


PostRendererComponent::$instance = new PostRendererComponent();
