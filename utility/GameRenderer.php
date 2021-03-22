<?php
/**
 * Static class that holds game rendering functions
 */
class GameRenderer {

    /**
     * Renders game as HTML
     */
    static function RenderGame($game) {
        // DEBUG -> Game should be get in step before
        $game = new Game(
            0,
            "Minecraft",
            new User(
                0,
                "Notch",
                "Markus",
                "Persson"),
            "Blocky little game!",
            array("windows" => true),
            "Beta 1.8.1"
            );
        ?>

        <div class="row border">
            <div class="md-12">
                <h1 class="game-display">
                    <span>
                        <?= $game->getName() ?>
                    </span>
                    <span class="game-version">
                        <?= $game->getVersion() ?>
                    </span>
                </h1>
            </div>
            <div class="col-12">
                <!-- SCREENSHOTS -->
            </div>
            <div class="col-12">
                <p class="game-display">
                    <?= $game->getDescription() ?>
                </p>
            </div>
            <!-- DOWNLOADS -->
            <div class="col-8 row">
                <h2 class="game-display">Downloads</h2>
                <?php
                // If Windows:
                if($game->hasWindows()) {
                    ?>
                    <div class="col-6">
                        Windows
                    </div>
                    <div class="col-6">
                        <button>
                            Download
                        </button>
                    </div>
                    <?php
                }
                // If MAC-OS:
                if($game->hasMac()) {
                    ?>
                    <div class="col-6">
                        Mac
                    </div>
                    <div class="col-6">
                        <button>
                            Download
                        </button>
                    </div>
                    <?php
                }
                // If Linux:
                if($game->hasLinux()) {
                    ?>
                    <div class="col-6">
                        Linux
                    </div>
                    <div class="col-6">
                        <button>
                            Download
                        </button>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>

        <?php
    }
}
?>
