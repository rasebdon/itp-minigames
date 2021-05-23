<?php
/**
 * Class that manages the initial game upload
 */
class GameEditInterface {

    /** @var GameEditInterface */
    public static $instance;

    function __construct()
    {
        if(!isset($_GET['action'])) 
            return;
     
        switch($_GET['action']) {
            case "editGameInterface":
                if(isset($_GET['id']) && $_GET['id'] != null && isset($_SESSION['UserID']) && $_SESSION['UserID'] != null)
                    $this->showForm(GameService::$instance->getGame($_GET['id']));
                break;
        }
    }

    function showForm(Game $game) {
        if($game == null || $_SESSION['UserID'] != $game->getAuthor()->getID()) 
            return;
        
        // HTML EDIT FORM
        ?>
            <h1 class="mb-5">Edit <?= $game->getTitle()?></h1>
            <form method="post" enctype="multipart/form-data" action="index.php?action=editGame?id=<?=$game->getId()?>">
                <div class="mb-3">
                    <label for="game-description" class="form-label">Description</label>
                    <textarea type="text" class="form-control" name="game-description" id="game-description" aria-describedby="game-description"><?=$game->getDescription()?></textarea>
                </div>
                <div class="mb-3">
                    <label for="game-version" class="form-label">Version</label>
                    <input type="text" class="form-control" name="game-version" id="game-version" aria-describedby="game-version" value="<?=$game->getVersion()?>">
                </div>
                <div class="mb-3">
                    <label for="game-genres" class="form-label">Genre</label>
                    <div id="game-genres" class="dropdown-check-list" tabindex="100">
                        <span onclick='$("#genres").toggle();' class="anchor">Select Genres</span>
                        <ul class="items show" id="genres">
                            <?php
                                // Get genres from database and print selection
                                $genres = GameService::$instance->getAllGenres();
                                for ($i = 0; $i < sizeof($genres); $i++) { 
                                    $genre = $genres[$i];
                                    echo '<li><input id="genre-' . $genre['Name'] . '" class="form-check-input" type="checkbox" name="game-genres[]" value="' . $genre['GenreID'] . '"><label  class="form-check-label ms-2" for="genre-' . $genre['Name'] . '">' . $genre['Name'] . '</label></li>';
                                }
                            ?>
                        </ul>
                    </div>
                </div>
                <div class="mb-3">
                    <h2>Upload new Game version as .zip or .rar file</h2>
                    <label for="game-file-windows" class="form-label">Windows</label>
                    <input class="form-control" type="file" id="game-file-windows" name="game-file-windows">
                    <label for="game-file-linux" class="form-label">Linux</label>
                    <input class="form-control" type="file" id="game-file-linux" name="game-file-linux">
                    <label for="game-file-mac" class="form-label">Mac OS</label>
                    <input class="form-control" type="file" id="game-file-mac" name="game-file-mac">
                </div>
                <button type="submit" class="btn btn-primary">Create</button>
            </form>
        <?php
    }    
}

GameEditInterface::$instance = new GameEditInterface();
