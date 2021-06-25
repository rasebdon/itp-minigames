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
                if(isset($_POST['TogglePostLike'])){
                    ForumService::$instance->toggleLike($_POST['TogglePostLike'], $_SESSION['UserID']);
                }
                if(isset($_POST['TogglePostDislike'])){
                    ForumService::$instance->toggleDislike($_POST['TogglePostDislike'], $_SESSION['UserID']);
                }
                if(isset($_POST['DeletePost'])){
                    ForumService::$instance->deletePost($_POST['DeletePost']);
                }
                if(isset($_POST['postPost']) && !empty($_POST['PostText']) && !empty($_POST['PostTitle'])){
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
                }else{
                    $_SESSION['PostError']['textEmpty'] = "<div class='mt-1 alert alert-danger' role='alert'> Can't be empty </div>";
                }
                $this->RenderForum($_GET['id']);
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
        <div class="p-4 p-md-5 mb-4 rounded forum-center-backround" style="background-image: url('<?= $banner ?>');
                    background-repeat: no-repeat;
                    background-position: center;">
          </div>

        <div class="forum-header col-md-6 px-0">
            <h1 class="display-4"><?= $game->getTitle()?></h1>  
            <p class="mb-0 forum-linktogame">
                <a href="index.php?action=viewGame&id=<?= $game->getId()?>" class="button button--primary">View Game</a>
            </p>   
        </div>

        

        <?php
            if(isset($_SESSION['UserID']) &&  $_SESSION['UserID'] != null){
                $this->displayPostForm($forumid);
            }

        ?>

        <form id="TogglePostLike" action="index.php?action=forum&id=<?= $forumid ?>" method="POST"></form>
        <form id="TogglePostDislike" action="index.php?action=forum&id=<?= $forumid ?>" method="POST"></form>
        <form id="DeletePost" action="index.php?action=forum&id=<?= $forumid ?>" method="POST"></form>


        <?php
        foreach ($posts as $post) {
        ?>
            
            <div class="post-banner md-12 mb-3 border-bottom rounded row" onclick="location.href='index.php?action=post&id=<?= $post->getId() ?>';">
                <div class="col-10 bl-1">
                    <span class="h1 d-block "><?= $post->getTitle() ?>
                        <?php
                        if(isset($_SESSION['UserID']) &&  $_SESSION['UserID'] != null){
                            if(UserService::$instance->getUser($_SESSION['UserID'])->getUserType()->getAccessStrength() >= UserType::Admin()->getAccessStrength()){
                                ?>
                                        <button type='submit' class='btn' value='<?= $post->getId() ?>' form='DeletePost' name='DeletePost'>
                                            <i class="fa fa-trash" style="color:red"></i>
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
                    if(isset($_SESSION['UserID']) &&  $_SESSION['UserID'] != null){                        
                        if(ForumService::$instance->isPostRated($post->getId(), $_SESSION['UserID'], 1)){
                            ?>
                                <button type='submit' class='btn' value='<?= $post->getId() ?>' form='TogglePostLike' name='TogglePostLike'>
                                    <i class="fa fa-arrow-circle-up" style="color:blue"></i>
                                </button>
                            <?php
                        }else{
                            ?>
                                <button type='submit' class='btn' value='<?= $post->getId() ?>' form='TogglePostLike' name='TogglePostLike'>
                                    <i class="fa fa-arrow-circle-up" style="color:black"></i>
                                </button>
                            <?php
                        }
                    }else{
                        echo '<i class="fa fa-arrow-circle-up" style="color:black"></i>';
                    }                    
                ?>
                    <div><?= $post->getUpvotes() ?></div>
                <?php
                    if(isset($_SESSION['UserID']) &&  $_SESSION['UserID'] != null){                        
                        if(ForumService::$instance->isPostRated($post->getId(), $_SESSION['UserID'], 0)){
                            ?>
                                <button type='submit' class='btn' value='<?= $post->getId() ?>' form='TogglePostDislike' name='TogglePostDislike'>
                                    <i class="fa fa-arrow-circle-down" style="color:blue"></i>
                                </button>
                            <?php
                        }else{
                            ?>
                                <button type='submit' class='btn' value='<?= $post->getId() ?>' form='TogglePostDislike' name='TogglePostDislike'>
                                    <i class="fa fa-arrow-circle-down" style="color:black"></i>
                                </button>
                            <?php
                        }
                    }else{
                        echo '<i class="fa fa-arrow-circle-down" style="color:black"></i>';
                    }
                    echo '</div>';

                    //check if have the rights to delete a Post 
                    ?>

                    
               
            </div>
<?php
        }
    }

    function displayPostForm(int $forumid){
        ?>
        <div class="col-md-12">
            <p>Create your own post:</p>
            <form id="postPost" action="index.php?action=forum&id=<?= $forumid ?>" method="POST">
                <input type="text" name="PostTitle" id="PostTitle" class="form-control form__input mb-2" placeholder="Your Post needs a title">
                <textarea name="PostText" id="PostText" class="form-control form__input" placeholder="Text of your post" cols="40" rows="5"></textarea><br>
                <small><?= $_SESSION['PostError']['textEmpty'] ?? '' ?></small>
                <button class="btn-primary btn" id="postPost" name="postPost">Post</button>
            </form>
        </div>
        <?php
    }
}

ForumRendererComponent::$instance = new ForumRendererComponent();
