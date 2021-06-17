<?php

/**
 * Class that manages the initial game upload
 */
class GameUploadInterface
{

    /** @var GameUploadInterface */
    public static $instance;

    function __construct()
    {
        if (!isset($_GET['action']))
            return;

        switch ($_GET['action']) {
            case "uploadGameInterface":
                $this->showForm();
                break;
            case "uploadGame":
                /*
                GameService::$instance->uploadGame(); 
                break;
                */
        }
    }

    function showForm()
    {
        // HTML FORM

?>
        <div class="heading-primary">
            <h1 class="heading-primary__text">creator dashboard</h1>
        </div>
        <section class="game-upload">
            <h2 class="heading-secondary">Upload Game</h2>
            <form class="form" method="post" enctype="multipart/form-data" action="index.php?action=uploadGame">
                <div class="form__group">
                    <input type="text" class="form__input" placeholder="Game Title" name="game-title" id="game-title" aria-describedby="game-title" value="<?= $_SESSION['gameUpload']['game-title'] ?? "" ?>">
                    <label for="game-title" class="form__label">Game Title</label>
                    <span class="form__separator"></span>
                </div>
                <small><?= $_SESSION['uploadGameErrors']['game-title'] ?? '' ?></small>
                <div class="form__group">
                    <textarea type="text" placeholder="Description" class="form__input" name="game-description" id="game-description" aria-describedby="game-description"><?= $_SESSION['gameUpload']['game-description'] ?? "" ?></textarea>
                    <label for="game-description" class="form__label">Description</label>
                    <span class="form__separator"></span>
                </div>
                <div class="row">
                    <div class="col-12 col-lg-6">
                        <div class="form__group">
                            <input type="text" class="form__input" name="game-version" id="game-version" autocomplete="off" aria-describedby="game-version" placeholder="Version">
                            <label for="game-version" class="form__label">Version</label>
                            <span class="form__separator"></span>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div id="game-genres" class="dropdown-check-list" tabindex="100">
                            <span onclick='$("#genres").toggle();' class="anchor">Select Genres</span>
                            <ul class="items" style="display:none;" id="genres">
                                <?php

                                // Get genres from database and print selection
                                $genres = GameService::$instance->getAllGenres();
                                for ($i = 0; $i < sizeof($genres); $i++) {
                                    $genre = $genres[$i];
                                ?>
                                    <li>
                                        <input id="genre-<?= $genre['Name'] ?>" class="checkbox" type="checkbox" name="game-genres[]" value="<?= $genre['GenreID'] ?>">
                                        <label for="genre-<?= $genre['Name'] ?>"><?= $genre['Name'] ?></label>
                                    </li>
                                <?php
                                }
                                ?>

                            </ul>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <h2>Upload new Game version as .zip or .rar file</h2>
                    <div class="form__group">
                        <label for="game-file-windows" class="form__label--file">Windows</label>
                        <input class="form-control" type="file" id="game-file-windows" name="game-file-windows">
                    </div>
                    <div class="form__group">
                        <label for="game-file-linux" class="form__label--file">Linux</label>
                        <input class="form-control" type="file" id="game-file-linux" name="game-file-linux">
                    </div>
                    <div class="form__group">
                        <label for="game-file-mac" class="form__label--file">Mac OS</label>
                        <input class="form-control" type="file" id="game-file-mac" name="game-file-mac">
                    </div>
                </div>
                <small><?= $_SESSION['uploadGameErrors']['game-file'] ?? '' ?></small>
                <button type="submit" class="button button--primary" name="uploadGame">Create</button>
            </form>
        </section>
<?php
    }
}

GameUploadInterface::$instance = new GameUploadInterface();
