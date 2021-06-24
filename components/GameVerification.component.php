<?php
class GameVerificationComponent
{
    /** @var GameVerificationComponent */
    public static $instance;

    function __construct()
    {
        // Check for delete game
        if (isset($_GET['deleteGame'])) {
            GameService::$instance->deleteGame($_GET['deleteGame']);
        }
        // Check for verify game
        if (isset($_GET['verifyGame'])) {
            GameService::$instance->verifyGame($_GET['verifyGame']);
        }
        // Route GET variables
        if (!isset($_GET['action']))
            return;
        switch ($_GET['action']) {
            case "listGamesToVerify":
                if (isset($_GET['offset']) && isset($_GET['amount']))
                    $this->showGamesToVerify($_GET['offset'], $_GET['amount']);
                break;
            case "searchVerificationGames":
                if (isset($_GET['gameTitle']))
                    $this->searchGames($_GET['gameTitle']);
                break;
        }
    }

    /**
     * Shows the next set of games from the offset and amount given
     */
    public function showGamesToVerify($offset = 0, $amount = 20)
    {
?>

        <div class="heading-primary">
            <h1 class="heading-primary__text">Game Verification</h1>
        </div>
        <?php
        // Check if games can be loaded (games amount > 0)
        if (($gamesAmount = GameService::$instance->getGamesCount(false, false)) == 0) {
        ?>
            <div class="text-box text-box--1">
                <p class="center">Currently there are no games that need verification!<br> Check back later.</p>
            </div>
        <?php
            return;
        }
        ?>
        <div class="row game-verification-list">
            <div class="mb-3 col-12 text-center">
                <h1 class="display-3">Game Verification List</h1>
            </div>
            <div class="game-verification-list-search mb-5 mt-3">
                <form class="d-flex row justify-content-center">
                    <div class="mt-3 row col-6">
                        <div class="col-10">
                            <input class="d-block form-control me-2" name="gameTitle" id="search-id" type="search" placeholder="Search Game Title" aria-label="Search">
                        </div>
                        <input type="hidden" name="action" value="searchVerificationGames">
                        <button class="btn btn-outline-success col-2" type="submit">Search</button>
                    </div>
                </form>
            </div>
            <div class="game-box col-12 row game-verification-list-item">
                <div class="col-2 fw-bold">ID</div>
                <div class="col-2 fw-bold">Title</div>
                <div class="col-2 fw-bold">Author</div>
                <div class="col-2 fw-bold">Version</div>
                <div class="col-1 fw-bold">Delete</div>
                <div class="col-1 fw-bold">Verify</div>
                <div class="col-1 fw-bold">View</div>
            </div>
            <?php

            // Get all unverfied games
            $games = GameService::$instance->getGames($offset = 0, $amount = 20, false, false);

            // Render all games that were loaded
            /** @var Game $game */
            foreach ($games as $game) {
            ?>
                <div class="game-box col-12 row game-verification-list-item">
                    <div class="col-2"><?= $game->getId() ?></div>
                    <div class="col-2"><?= $game->getTitle() ?></div>
                    <div class="col-2"><a href="<?= "?action=showUser&id=" . $game->getAuthor()->getId() ?>"><?= $game->getAuthor()->getUsername() ?></a></div>
                    <div class="col-2"><?= $game->getVersion() ?></div>
                    <div class="col-1">
                        <a href="<?= "?action=listGamesToVerify&amount=$amount&offset=$offset&deleteGame=" . $game->getId() ?>" class="btn btn-danger">X</a>
                    </div>
                    <div class="col-1">
                        <a href="<?= "?action=listGamesToVerify&amount=$amount&offset=$offset&verifyGame=" . $game->getId() ?>" class="btn btn-success">&#10003;</a>
                    </div>
                    <div class="col-1">
                        <a href="?action=viewGame&id=<?= $game->getId() ?>" class="btn btn-primary"><i class="bi bi-search"></i></a>
                    </div>
                </div>
            <?php
            }

            // Add buttons
            ?>
            <div class="mt-5 col-12 row p-0">
                <div class="mt-2 mb-2 col-12">
                    <p>Showing games <?= $offset + 1 ?> - <?= $offset + sizeof($games) ?> ( <?= $gamesAmount ?> total )</p>
                </div>
                <div class="d-flex col-6 justify-content-start p-0">
                    <?php
                    // If there are any game left that can be loaded before ( > amount ) then show button
                    if ($offset - $amount >= 0) {
                        // Add load last amount
                    ?>
                        <a type="button" class="btn btn-primary" href="/?action=listGamesToVerify&amount=<?= $amount ?>&offset=<?= $offset - $amount > 0 ? $offset - $amount : 0 ?>">
                            Last <?= $amount ?> Games
                        </a>
                    <?php

                    }
                    ?>
                </div>
                <div class="d-flex col-6 justify-content-end p-0">
                    <?php
                    // If there are any games left that can be loaded after ( < total games in db ) then show button
                    if ($amount + $offset < $gamesAmount) {
                        // Add load next amount
                    ?>
                        <a type="button" class="btn btn-primary" href="/?action=listGamesToVerify&amount=<?= $amount ?>&offset=<?= $offset + $amount ?>">
                            Next <?= $amount ?> Games
                        </a>
                    <?php
                    }
                    ?>
                </div>
            </div>
        </div>
    <?php
    }

    /**
     * Shows all games that are meeting the searched criteria
     */
    public function searchGames($searchTitle)
    {
        // Check if games can be loaded (games amount > 0)
        if (($gamesAmount = GameService::$instance->getGamesCount()) == 0)
            return;

    ?>
        <div class="row game-verification-list">
            <div class="mb-3 col-12 text-center">
                <h1 class="display-3">Game Verification List</h1>
            </div>
            <div class="game-verification-search mb-4 mt-3">
                <form class="d-flex row justify-content-center">
                    <div class="mt-3 row col-6">
                        <div class="col-10">
                            <input class="d-block form-control me-2" name="gameTitle" id="search-id" type="search" placeholder="Search Game Title" aria-label="Search">
                        </div>
                        <input type="hidden" name="action" value="searchVerificationGames">
                        <button class="btn btn-outline-success col-2" type="submit">Search</button>
                    </div>
                </form>
            </div>
            <div class="game-verification-search mb-2 text-center">
                <a class="btn btn-success" type="button" href="/?action=listGamesToVerify&offset=0&amount=20">Show all</a>
            </div>
            <div class="game-verification-search mb-5 mt-3 text-center">
                <h2>Showing results for "<?= $searchTitle ?>"</h2>
            </div>
            <div class="game-box col-12 row game-verification-list-item">
                <div class="col-2 fw-bold">ID</div>
                <div class="col-2 fw-bold">Title</div>
                <div class="col-2 fw-bold">Author</div>
                <div class="col-2 fw-bold">Version</div>
                <div class="col-1 fw-bold">Delete</div>
                <div class="col-1 fw-bold">Verify</div>
                <div class="col-1 fw-bold">View</div>
            </div>
            <?php

            // Load the games from the service class
            $games = GameService::$instance->searchGames($searchTitle, false, false);

            if ($games == null || sizeof($games) == 0) {
            ?>
                <div class="game-box col-12 text-center">
                    <p class="h4 mt-5">No results found</p>
                </div>
            <?php
                return;
            }

            // Render all games that were loaded
            /** @var Game $game */
            foreach ($games as $game) {
            ?>
                <div class="game-box col-12 row game-verification-list-item">
                    <div class="col-2"><?= $game->getId() ?></div>
                    <div class="col-2"><?= $game->getTitle() ?></div>
                    <div class="col-2"><a href="<?= "?action=showUser&id=" . $game->getAuthor()->getId() ?>"><?= $game->getAuthor()->getUsername() ?></a></div>
                    <div class="col-2"><?= $game->getVersion() ?></div>
                    <div class="col-1">
                        <a href="<?= "?action=listGamesToVerify&deleteGame=" . $game->getId() ?>" class="btn btn-danger">X</a>
                    </div>
                    <div class="col-1">
                        <a href="<?= "?action=listGamesToVerify&verifyGame=" . $game->getId() ?>" class="btn btn-success">&#10003;</a>
                    </div>
                    <div class="col-1">
                        <a href="?action=viewGame&id=<?= $game->getId() ?>" class="btn btn-primary"><i class="bi bi-search"></i></a>
                    </div>
                </div>
            <?php
            }
            ?>
        </div>
        </div>
<?php
    }
}

GameVerificationComponent::$instance = new GameVerificationComponent();
