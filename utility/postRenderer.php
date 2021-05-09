<?php

/**
 * Static class that holds forum rendering functions
 */
class PostRenderer
{

    /** @var PostRenderer */
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

        //var_dump($post);

        if (isset($_POST['commentText']) && !empty($_POST['commentText'])) {

            //var_dump($_POST);
            $commentObj = new Comment(
                0,
                $_POST['commentText'],
                $post->getId(),
                UserService::$instance->getUser($_SESSION['UserID']),
                date("Y-m-d H:i:s")
            );

            ForumService::$instance->insertComment($commentObj);
        }

        $allComment = ForumService::$instance->getComments($post->getId());
        //var_dump($allComment);
?>
        <div id="thePost">
            <h1 class="display-4"><?= $post->getTitle() ?></h1>
            <h2><?= $post->getUser()->getUsername() ?></h2>
            <p><?= $post->getText() ?></p>
        </div>

        <?php
        if (isset($_SESSION['UserID']) &&  $_SESSION['UserID'] != null) {
        ?> <div class"row">
                <div class="col-md-12">
                    <form method="POST">
                        <textarea name="commentText" id="mainComment" class="form-control" placeholder="add comment - be friendly" cols="40" rows="5"></textarea><br>
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
                            </div>
                        </div>
                    </div>
                </div>
<?php
            }
        }
    }
}

PostRenderer::$instance = new PostRenderer();
