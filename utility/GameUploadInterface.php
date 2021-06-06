<?php
/**
 * Class that manages the initial game upload
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
                    <h2>Upload Images</h2>
                    <input class="form-control" multiple="multiple" type="file" id="image-files" name="image-files[]">
                </div>
                <div class="mb-3">
                    <h2>Upload Game as .zip or .rar file</h2>
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

GameUploadInterface::$instance = new GameUploadInterface();
