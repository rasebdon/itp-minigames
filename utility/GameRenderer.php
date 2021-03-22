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
                "Persson",
            array("twitter" => "https://twitter.com/notch/")),
            "Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. 
            \n\n\n
            Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. 
            
            Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. ",
            array("windows" => true),
            "Beta 1.8.1",
            4.722415
            );
        ?>

        <div class="row border game-display mt-5 mb-5">
            <div class="md-12">
                <h1>
                    <?= $game->getName() ?>
                    <span class="game-version">
                        <?= $game->getVersion() ?>
                    </span>
                </h1>
            </div>
            <div class="col-12 pb-2">
                <!-- RATING -->
                <?php 
                for ($i=0; $i < 5; $i++) { 
                    echo '<span class="fa fa-star';
                    if($i < (int)$game->getRating())
                        echo ' checked';
                    echo '"></span>';
                }
                ?>
                <span class="rating"><?php printf("%.2f/5", $game->getRating()); ?></span>
            </div>
            <div class="col-12 border-top border-bottom pb-3 pt-3 screenshots">
                <!-- SCREENSHOTS -->
                <!-- DEBUG -->
                <div class="col-8 offset-2 mt-3 mb-3">
                    <img src="https://news.xbox.com/de-de/wp-content/uploads/sites/3/2020/04/Minecraft-RTX-Beta_Hero.jpg?fit=1920%2C1080" alt="screenshot01" width="100%">
                </div>
            </div>
            <div class="col-12 pt-3">
                <p>
                    <?= $game->getDescription() ?>
                </p>
            </div>
            <!-- DOWNLOADS -->
            <div class="col-10 mt-3 mb-3">
                <h2>Downloads</h2>
                <?php
                // If Windows:
                if($game->hasWindows()) {
                    ?>
                    <div class="row">
                        <div class="col-4 d-flex align-content-center flex-wrap">
                            <span>
                                Windows
                            </span>
                        </div>
                        <div class="col-6 text-end">
                            <button class="btn btn-download" onclick="download(<?= $game->getId() ?>)">
                                Download
                            </button>
                        </div>
                    </div>
                    <?php
                }
                // If MAC-OS:
                if($game->hasMac()) {
                    ?>
                    <div class="row">
                        <div class="col-4 d-flex align-content-center flex-wrap">
                            <span>
                                Mac
                            </span>
                        </div>
                        <div class="col-6 text-end">
                            <button class="btn btn-download" onclick="download(<?= $game->getId() ?>)">
                                Download
                            </button>
                        </div>
                    </div>
                    <?php
                }
                // If Linux:
                if($game->hasLinux()) {
                    ?>
                    <div class="row">
                        <div class="col-4 d-flex align-content-center flex-wrap">
                            <span>
                                Linux
                            </span>
                        </div>
                        <div class="col-6 text-end">
                            <button class="btn btn-download" onclick="download(<?= $game->getId() ?>)">
                                Download
                            </button>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
            <div class="col-2 text-center">
                <!-- SOCIAL MEDIA LINKS -->
                <?php
                // If Twitter 
                if($link = $game->getAuthor()->getTwitter()) {
                ?>
                <a href="<?= $link ?>">
                    <svg class="twitter" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-twitter" viewBox="0 0 16 16">
                        <path d="M5.026 15c6.038 0 9.341-5.003 9.341-9.334 0-.14 0-.282-.006-.422A6.685 6.685 0 0 0 16 3.542a6.658 6.658 0 0 1-1.889.518 3.301 3.301 0 0 0 1.447-1.817 6.533 6.533 0 0 1-2.087.793A3.286 3.286 0 0 0 7.875 6.03a9.325 9.325 0 0 1-6.767-3.429 3.289 3.289 0 0 0 1.018 4.382A3.323 3.323 0 0 1 .64 6.575v.045a3.288 3.288 0 0 0 2.632 3.218 3.203 3.203 0 0 1-.865.115 3.23 3.23 0 0 1-.614-.057 3.283 3.283 0 0 0 3.067 2.277A6.588 6.588 0 0 1 .78 13.58a6.32 6.32 0 0 1-.78-.045A9.344 9.344 0 0 0 5.026 15z"/>
                    </svg>
                </a>
                <?php
                }
                ?>
            </div>
        </div>

        <?php
    }
}
?>
