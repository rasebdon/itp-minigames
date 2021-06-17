<?php

/**
 * Class that manages the initial game upload
 */
class GameEditInterface
{

    /** @var GameEditInterface */
    public static $instance;

    function __construct()
    {
        if (!isset($_GET['action']) && !isset($_POST['GamePictureSubmit']))
            return;

        if (isset($_POST['GamePictureSubmit'])) {
            $rightToEdit = $_SESSION['UserID'] == GameService::$instance->getGame($_POST['id'])->getAuthor()->getId();
            if (
                isset($_POST['id']) && $_POST['id'] != null &&
                isset($_SESSION['UserID']) && $_SESSION['UserID'] != null &&
                $rightToEdit
            ) {
                GameService::$instance->addScreenshot($_POST['id'], $_FILES['file']);
            }
        }

        switch ($_GET['action']) {
            case "editGameInterface":
                if (isset($_GET['id']) && $_GET['id'] != null && isset($_SESSION['UserID']) && $_SESSION['UserID'] != null)
                    $this->showForm(GameService::$instance->getGame($_GET['id']));
                break;
            case "editGame":
                if(!isset($_GET['id']) || $_GET['id'] != null || !isset($_SESSION['UserID']) || $_SESSION['UserID'] == null)
                    break;

                // Check if game can be edited (From author or admin)
                $rightToEdit = (($game = GameService::$instance->getGame($_GET['id'])) != null && $_SESSION['UserID'] == $game->getAuthor()->getId()) ||
                    UserService::$instance->getUser($_SESSION['UserID'])->getUserType()->getAccessStrength() == UserType::Admin()->getAccessStrength();                
                if(isset($_GET['id']) && $_GET['id'] != null &&
                isset($_SESSION['UserID']) && $_SESSION['UserID'] != null &&
                $rightToEdit) {
                    if(isset($_GET['deleteGame']))
                        GameService::$instance->deleteGame($_GET['id']);
                    else if (isset($_GET['deleteScreenshot'])) {
                        GameService::$instance->deleteScreenshot($_GET['deleteScreenshot']);
                        echo "<script>location.replace('index.php?action=editGameInterface&id=" . $_GET['id'] . "');</script>";
                    } else
                        GameService::$instance->editGame();
                }
                break;
        }
    }

    function showForm(Game $game)
    {
        if ($game == null || $_SESSION['UserID'] != $game->getAuthor()->getID())
            return;

        // HTML EDIT FORM
?>
        <div class="heading-primary">
            <h1 class="heading-primary__text">creator dashboard</h1>
        </div>
        <section class="game-edit">

            <div class="game-edit__general">
                <h2 class="heading-secondary">Edit <?= $game->getTitle() ?></h2>
                <h3 class="heading-tertiary">General</h3>
                <form class="form" method="post" enctype="multipart/form-data" action="index.php?action=editGame&id=<?= $game->getId() ?>">
                    <div class="form__group">
                        <textarea type="text" placeholder="Description" class="form__input" name="game-description" id="game-description" aria-describedby="game-description"><?= $game->getDescription() ?></textarea>
                        <label for="game-description" class="form__label">Description</label>
                        <span class="form__separator"></span>
                    </div>
                    <div class="row">
                        <div class="col-12 col-lg-6">
                            <div class="form__group">
                                <input type="text" class="form__input" name="game-version" id="game-version" autocomplete="off" aria-describedby="game-version" placeholder="Version" value="<?= $game->getVersion() ?>">
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
                                    $gamesGenres = $game->getGenres();

                                    for ($i = 0; $i < sizeof($genres); $i++) {
                                        $genre = $genres[$i];
                                        $checked = false;

                                        for ($j = 0; $j < sizeof($gamesGenres); $j++) {
                                            if ($gamesGenres[$j] == $genre['Name']) {
                                                $checked = true;
                                            }
                                        }
                                    ?>
                                        <li>
                                            <input id="genre-<?= $genre['Name'] ?>" class="checkbox" type="checkbox" name="game-genres[]" <?= ($checked ? ' checked' : '') ?> value="<?= $genre['GenreID'] . ($checked ? ' checked' : '') ?>">
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
                    <input type="hidden" name="game-id" value="<?= $game->getId() ?>">
                    <div class="flex">
                        <button type="submit" class="button button--primary">Save</button>
                        <a href="index.php?action=editGame&id=<?= $game->getId() ?>&deleteGame=<?= $game->getId() ?>" type="button" class="button button--primary mt-3">Delete</a>
                    </div>
                </form>
            </div>
            <div class="game-edit__screenshots">
                <h3 class="heading-tertiary">Screenshots</h3>
                <div id="cropGamePicture" class="crop">
                    <label for="profilePictureUpload" class="file__label">
                        Upload image to crop...
                    </label>
                    <input type="file" class="crop__input file" accept="image/*" name="crop-input" id="profilePictureUpload">
                    <div class="crop__cropper-container">
                        <img class="crop__cropper-image" data-src="" alt="">
                        <img class="crop__cropper-image-clipped" alt="">
                        <div class="crop__cropper">
                            <span class="crop__crophandle crop__crophandle--tl"></span>
                            <span class="crop__crophandle crop__crophandle--tr"></span>
                            <span class="crop__crophandle crop__crophandle--br"></span>
                            <span class="crop__crophandle crop__crophandle--bl"></span>
                        </div>
                        <div class="crop__crop-overlay"></div>
                    </div>
                    <form method="POST" enctype="multipart/form-data" class="crop__form">
                        <input type="hidden" name="GamePictureSubmit" value="">
                        <input type="hidden" name="id" value="<?= $game->getId() ?>">
                        <button type="button" class="crop__submit button button--primary">Crop</button>
                    </form>
                </div>
                <script>
                    new Crop(document.getElementById("cropGamePicture"), 16 / 9);
                </script>
                <?php
                $screenshots = $game->getScreenshots();

                if (sizeof($screenshots) > 0) {
                ?>
                    <div id="editGameScreenshots" data-bs-ride="false" data-bs-pause="false" class="carousel slide carousel-fade game-edit__carousel">
                        <div class="carousel-inner">
                            <?php
                            for ($i = 0; $i < sizeof($screenshots); $i++) {
                            ?>
                                <div class="carousel-item <?= !$i ? 'active' : '' ?>">
                                    <div class="carousel__ui-container">
                                        <a href="index.php?action=editGame&id=<?= $game->getId() ?>&deleteScreenshot=<?= $screenshots[$i] ?>" class="button button--primary game__screenshot-delete">Delete</a>
                                        <!-- add "make thumbnail" button here -->
                                    </div>
                                    <img src="<?= $screenshots[$i] ?>" class="carousel-img" alt="...">
                                </div>
                            <?php
                            }
                            ?>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#editGameScreenshots" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#editGameScreenshots" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div>
                <?php
                }
                ?>
            </div>
        </section>
<?php
    }
}

GameEditInterface::$instance = new GameEditInterface();
