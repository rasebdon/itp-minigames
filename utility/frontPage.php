<?php
if (isset($_GET['action'])) {
    if ($_GET['action'] == 'viewFrontPage') {
       
            echo "DEBUG VERSION - DISABLE ON RELEASE";
            $game1 = new Game(
                0,
                "Minecraft1",
                new User(
                    0,
                    "Notch1",
                    "Markus",
                    "Persson",
                array(  "twitter" => "https://twitter.com/notch/",
                        "instagram" => "https://instagram.com/notchite/"),
                    UserType::Creator()),
                "Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. 
                \n\n\n
                Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. 
                
                Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. ",
                array("windows" => true),
                "RTX Windows 10 Beta",
                4.722415,
                array(  0 => "https://news.xbox.com/de-de/wp-content/uploads/sites/3/2020/04/Minecraft-RTX-Beta_Hero.jpg?fit=1920%2C1080",
                        1 => "https://i1.wp.com/www.minecraftrocket.com/wp-content/uploads/2015/03/LikeMinecraft-Shaders-Screenshot-1.png"),
                1
                );
            $game2 = new Game(
                0,
                "Minecraft2",
                new User(
                    0,
                    "Notch2",
                    "Markus2",
                    "Persson2",
                array(  "twitter" => "https://twitter.com/notch/",
                        "instagram" => "https://instagram.com/notchite/"),
                    UserType::Creator()),
                "Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. 
                \n\n\n
                Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. 
                
                Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. ",
                array("windows" => true),
                "RTX Windows 10 Beta",
                4.722415,
                array(  0 => "https://news.xbox.com/de-de/wp-content/uploads/sites/3/2020/04/Minecraft-RTX-Beta_Hero.jpg?fit=1920%2C1080",
                        1 => "https://i1.wp.com/www.minecraftrocket.com/wp-content/uploads/2015/03/LikeMinecraft-Shaders-Screenshot-1.png"),
                1
                );
            $game3 = new Game(
                0,
                "Minecraft3",
                new User(
                    0,
                    "Notch3",
                    "Markus3",
                    "Persson3",
                array(  "twitter" => "https://twitter.com/notch/",
                        "instagram" => "https://instagram.com/notchite/"),
                    UserType::Creator()),
                "Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. 
                \n\n\n
                Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. 
                
                Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. ",
                array("windows" => true),
                "RTX Windows 10 Beta",
                4.722415,
                array(  0 => "https://news.xbox.com/de-de/wp-content/uploads/sites/3/2020/04/Minecraft-RTX-Beta_Hero.jpg?fit=1920%2C1080",
                        1 => "https://i1.wp.com/www.minecraftrocket.com/wp-content/uploads/2015/03/LikeMinecraft-Shaders-Screenshot-1.png"),
                1
                );
            $game4 = new Game(
                0,
                "Minecraft4",
                new User(
                    0,
                    "Notch4",
                    "Markus4",
                    "Persson4",
                array(  "twitter" => "https://twitter.com/notch/",
                        "instagram" => "https://instagram.com/notchite/"),
                    UserType::Creator()),
                "Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. 
                \n\n\n
                Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. 
                
                Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. ",
                array("windows" => true),
                "RTX Windows 10 Beta",
                4.722415,
                array(  0 => "https://news.xbox.com/de-de/wp-content/uploads/sites/3/2020/04/Minecraft-RTX-Beta_Hero.jpg?fit=1920%2C1080",
                        1 => "https://i1.wp.com/www.minecraftrocket.com/wp-content/uploads/2015/03/LikeMinecraft-Shaders-Screenshot-1.png"),
                1
                );
            $games_debug = array($game1, $game2, $game3, $game4, $game1, $game2, $game3, $game4);
        //--HTML--
    ?>

        
            <h1 class="headerfrontPage text-center shadow p-3 mb-5 bg-body rounded text-primary">ITP-Minigames</h1>
            <div class="container border gamePreviewContainer">
              <!--Note: These will be several rows with several Items -->
                <div class="row gamePreviewRow">
                    
                    <?php
                    
                    foreach($games_debug as $game){
                        $screenshots = $game->getScreenshots();
                    
                        /*To-Do :
                            #Link to Game
                            #Link to Author
                        */
                    echo '<div class="col-3 border game-display mt-2 mb-2" >
                            <div class="sm-3 mb-2">
                                <h5 class="m-0">
                                    <span class="d-inline-block">'. $game->getName() .'</span>
                                    <span class="d-inline-block game-version">
                                        '. $game->getVersion() .'
                                    </span>
                                </h5>
                            </div>
                            <div class="row">    
                                <div class="col-6">
                                    
                                    <span class="author">Author: '. $game->getAuthor()->getUsername() .'</span>
                                </div>
                                <div class="col-6 pb-1">
                                ';
                               
                                for ($i=0; $i < 5; $i++) { 
                                    echo '<span class="fa fa-star';
                                    if($i < (int)$game->getRating())
                                        echo ' checked';
                                    echo '"></span>';
                                }
                                echo'<span class="rating">'; printf("%.2f/5", $game->getRating()); echo'</span>
                                </div>
                            </div>
                            <div class="col-12 border-top border-bottom pb-3 pt-3 screenshots">
                                <!-- SCREENSHOTS -->
                                <div class="col-12  mt-1 mb-1 ">
                                    <div class="thumbnail-container">
                                        
                                        <img src="'.$screenshots[0].'" alt="Game Preview not loaded" class="img-fluid max-width: 100%">
                                    
                                    </div>
                                </div>
                            </div>
                        </div>';
                       
                    } 
                    unset($game);
                    ?>
                </div>
                    

    <?php
            
    }
}