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

                if ($games == null) {
?>
                    <h1 class="text-center mt-5">There are no forums yet! Check back later.</h1>
                <?php
                    return;
                }

                //display a banner for each game with link to game forum 
                foreach ($games as $game) {
                ?>
                    <div class="forum-banner md-12 mb-3 d-flex justify-content-between" onclick="location.href='index.php?action=forum&id=<?= GameService::$instance->getForumID($game) ?>';">
                        <h1 class="m-0">
                            <span class="d-inline-block"><?= $game->getTitle() ?></span>
                            <span class="d-inline-block text-muted forum-banner-author">by
                                <?= $game->getAuthor()->getUsername(); ?>
                            </span>
                        </h1>
                        <div class="img-wrap">
                            <img src="<?= $game->getFirstScreenshot() ?>" />
                        </div>
                        <h4 class="m-0"><span class="d-inline-block d-none d-md-block">
                                Posts:
                                <?= ForumService::$instance->getNumberOfPosts(GameService::$instance->getForumID($game)) ?>
                            </span></h2>
                    </div>

<?php
                }
            } else {
                require_once "components/ForumRenderer.component.php";
            }
        }
    }
}
// INIT COMPONENT
ForumMainPageComponent::$instance = new ForumMainPageComponent();
