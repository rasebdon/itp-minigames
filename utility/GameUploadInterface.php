<?php
/**
 * Class that holds game rendering functions
 */
class GameUploadInterface {

    /** @var GameUploadInterface */
    public static $instance;

    function __construct()
    {
        if(!isset($_GET['action'])) 
            return;
     
        switch($_GET['action']) {
            case "uploadGameInterface":
                $this->showForm();
                break;
            case "uploadGame":
                GameService::$instance->uploadGame();
                break;
        }
    }

    function showForm() {
        // HTML FORM
        ?>
            <h1 class="mb-5">Upload Game</h1>
            <form method="post" enctype="multipart/form-data" action="index.php?action=uploadGame">
                <div class="mb-3">
                    <label for="game-title" class="form-label">Game Title</label>
                    <input type="text" class="form-control" name="game-title" id="game-title" aria-describedby="game-title">
                </div>
                <div class="mb-3">
                    <label for="game-description" class="form-label">Description</label>
                    <textarea type="text" class="form-control" name="game-description" id="game-description" aria-describedby="game-description"></textarea>
                </div>
                <div class="mb-3">
                    <label for="game-version" class="form-label">Version</label>
                    <input type="text" class="form-control" name="game-version" id="game-version" aria-describedby="game-version">
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
                    <label for="game-platforms" class="form-label">Platforms</label>
                    <div id="game-platforms" class="dropdown-check-list" tabindex="100">
                        <span onclick='$("#platforms").toggle();' class="anchor">Select Platforms</span>
                        <ul class="items show" id="platforms">
                            <?php
                                // Get platforms from database and print selection
                                $platforms = GameService::$instance->getAllPlatforms();
                                for ($i = 0; $i < sizeof($platforms); $i++) { 
                                    $platform = $platforms[$i];
                                    echo '<li><input id="platform-' . $platform['Name'] . '" class="form-check-input" type="checkbox" name="game-platforms[]" value="' . $platform['PlatformID'] . '"><label  class="form-check-label ms-2" for="platform-' . $platform['Name'] . '">' . $platform['Name'] . '</label></li>';
                                }
                            ?>
                        </ul>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="game-file" class="form-label">Upload Game as .zip or .rar file</label>
                    <input class="form-control" type="file" id="game-file" name="game-file">
                </div>
                <button type="submit" class="btn btn-primary">Create</button>
            </form>
        <?php
    }    
}

GameUploadInterface::$instance = new GameUploadInterface();
