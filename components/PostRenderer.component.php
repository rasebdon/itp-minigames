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

        if (isset($_POST['addComment']) && !empty($_POST['commentText'])) {

            if (isset($_SESSION['commentError']))
                    unset($_SESSION['commentError']);

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
        }else{
            $_SESSION['commentError']['emptyComment'] = "<div class='mt-1 alert alert-danger' role='alert'> Can't be empty </div>";
        }
        //var_dump($post);

        //var_dump($allComment);
        if (isset($_POST['DeleteComment'])) {

            ForumService::$instance->deleteComment($_POST['DeleteComment']);
        }

        if (isset($_POST['ToggleCommentLike'])) {
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
            <h1 class="heading-secondary"><?= $post->getTitle() ?></h1>
            <h2 class="heading-quaternary"><?= $post->getUser()->getUsername() ?></h2>
            <p><?= $post->getText() ?></p>
        </div>

        <?php
        if (isset($_SESSION['UserID']) &&  $_SESSION['UserID'] != null) {
        ?> <div class="row">
                <div class="col-md-12">
                    <form method="POST">
                        <textarea name="commentText" id="mainComment" class="form-control" placeholder="add comment - be friendly" cols="40" rows="5"></textarea><br>
                        <div style="text-align:right"><button class="button button--primary d-inline-block" id="addComment" name="addComment">Add comment</button></div>
                        <small><?= $_SESSION['commentError']['emptyComment'] ?? '' ?></small>
                    </form>
                </div>
            </div>
            <?php
        }

        if (!empty($allComment)) {
            if (count($allComment) == 1) {
            ?><h1>1 Comment </h1>
            <?php
            } else if (count($allComment) > 1) {
            ?><h1><?= count($allComment) ?> Comments </h1>
            <?php
            }
        } else {
            ?><h1>No comments added</h1>
            <?php
        }

        if (!empty($allComment)) {
            foreach ($allComment as $comment) {
            ?>
                <div class="row">
                    <div class="post-banner md-12 mb-3 border-bottom rounded row" style="max-height: initial;">
                        <div class="userComments">
                            <div class="comment row">
                                <div class="user col-9">
                                    <?= $comment->getAuthor()->getUsername() ?>

                                    <span class="h1 d-inline-block ">
                                        <?php
                                        //button for admin to delet comment 
                                        if (isset($_SESSION['UserID']) &&  $_SESSION['UserID'] != null) {
                                            if (UserService::$instance->getUser($_SESSION['UserID'])->getUserType()->getAccessStrength() >= UserType::Admin()->getAccessStrength()) {
                                        ?>
                                                <form>
                                                    <button type='submit' class='btn' value='<?= $comment->getId() ?>' form='DeleteComment' name='DeleteComment'>
                                                        <i class="fas fa-trash deleteButton"></i>
                                                    </button>
                                                </form>
                                        <?php
                                            }
                                        }
                                        ?>
                                    </span>
                                </div>

                                <div class="col-3 bl-1 text-end">
                                    <span class="time"><?= $comment->getDate() ?></span>

                                </div>

                                <div class="userComment col-11"><?= $comment->getText() ?></div>


                                <div class="d-inline-block upvote-display col-1" style="transform: unset; align-self:flex-start; position:unset;">
                                    <?php
                                    //likes
                                    if (isset($_SESSION['UserID']) &&  $_SESSION['UserID'] != null) {
                                        if (ForumService::$instance->isCommentRated($comment->getId(), $_SESSION['UserID'], 1)) {
                                    ?>
                                            <button type='submit' class='btn' value='<?= $comment->getId() ?>' form='ToggleCommentLike' name='ToggleCommentLike'>
                                                <i class="fas fa-plus likeButton"></i>
                                            </button>
                                        <?php
                                        } else {
                                        ?>
                                            <button type='submit' class='btn' value='<?= $comment->getId() ?>' form='ToggleCommentLike' name='ToggleCommentLike'>
                                                <i class=" fas fa-plus likeButton"></i>
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
                                                <i class="fas fa-minus" style="color: lightcoral; font-size: 25px;"></i>
                                            </button>
                                        <?php
                                        } else {
                                        ?>
                                            <button type='submit' class='btn' value='<?= $comment->getId() ?>' form='ToggleCommentDislike' name='ToggleCommentDislike'>
                                                <i class="fas fa-minus dislikeButton"></i>
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
<?php
            }
        }
    }
}


PostRendererComponent::$instance = new PostRendererComponent();
