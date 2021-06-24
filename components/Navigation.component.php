<?php
class NavigationComponent
{
    /** @var MyComponent */
    public static $instance;

    function __construct()
    {
        $this->showNavBar();
    }

    function showNavBar() {
        if(isset($_SESSION['UserID'])) {
            $user = UserService::$instance->getUser($_SESSION['UserID']);
            if($user != null)
                $userType = $user->getUserType();
        }
        $loggedIn = isset($_SESSION['UserID']);

        ?>
        <nav class="nav-top">
            <div class="nav-top__static">
                <div class="nav-top__toggle">
                    <input type="checkbox" class="nav-top__checkbox" id="nav-top-toggle">
                    <label for="nav-top-toggle" class="nav-top__button">
                        <span class="nav-top__icon"></span>
                    </label>
        
                    <div class="nav-side">
                        <ul class="nav-side__list">
                            <li class="nav-side__list-item nav-side__list-item--games">
                                <a class="button--secondary" href="index.php">Games</a>
                            </li>
                            <li class="nav-side__list-item nav-side__list-item--forum">
                                <a class="button--secondary" href="index.php?action=forum">Forum</a>
                            </li>
                            <?php
                            if ($loggedIn) {
                                $accessStrength = $userType->getAccessStrength();
                                $_SESSION['AccessStrength'] = $accessStrength;
                                // Normal user components
                                if ($accessStrength >= UserType::User()->getAccessStrength()) {
                            ?>
                                    <li class="nav-side__list-item nav-side__list-item--favorites">
                                        <a href="index.php?action=favorites" class="button--secondary">Favorites</a>
                                    </li>
                                    <li class="nav-side__list-item nav-side__list-item--edit-profile">
                                        <a class="button--secondary" href="index.php?action=editProfile">Profile</a>
                                    </li>
                                    <li class="nav-side__list-item nav-side__list-item--logout inverted">
                                        <a class="button--secondary" href="index.php?action=logout">Logout</a>
                                    </li>
        
                                <?php
                                }
                                // Game creator components
                                if ($accessStrength >= UserType::Creator()->getAccessStrength()) {
                                ?>
                                    <li class="nav-side__list-item nav-side__list-item--your-games">
                                        <a class="button--secondary" href="index.php?action=listCreatedGames">Your Games</a>
                                    </li>
                                <?php
                                }
                                // Admin components
                                if ($accessStrength >= UserType::Admin()->getAccessStrength()) {
                                ?>
                                    <li class="nav-side__list-item nav-side__list-item--user-list">
                                        <a class="button--secondary" href="index.php?action=showUsers&amount=20&offset=0">User List</a>
                                    </li>
                                    <li class="nav-side__list-item nav-side__list-item--game-verification">
                                        <a class="button--secondary" href="index.php?action=listGamesToVerify&amount=20&offset=0">Game Verification</a>
                                    </li>
                                <?php
                                }
                            } else {
                                // if someone isn´t logged in
                                ?>
                                <li class="nav-side__list-item nav-side__list-item--login">
                                    <a class="button--secondary" href="index.php?action=login">Login</a>
                                </li>
                                <li class="nav-side__list-item nav-side__list-item--register">
                                    <a class="button--secondary" href="index.php?action=register">Register</a>
                                </li>
                            <?php
                            }
                            ?>
                            <div class="nav-side__breaker"></div>
                            <li class="nav-side__list-item nav-side__list-item--imprint">
                                <a href="index.php?action=imprint">Imprint</a>
                            </li>
                            <li class="nav-side__list-item nav-side__list-item--contact">
                                <a href="index.php?action=contact">Contact</a>
                            </li>
                        </ul>
                    </div>
                    <div class="nav-overlay"></div>
                    <script>
                        const checkbox = document.querySelector(".nav-top__checkbox");
                        document.querySelector(".nav-overlay").addEventListener("click", () => {
                            checkbox.checked = false;
                        })
                    </script>
                </div>
                <span class="nav-top__logo">LG</span>
                <span class="nav-top__title">TITLE</span>
            </div>
            <ul class="nav-top__list">
                <li class="nav-top__list-item nav-top__list-item--games">
                    <a class="button--secondary" href="index.php">Games</a>
                </li>
                <li class="nav-top__list-item nav-top__list-item--forum">
                    <a class="button--secondary" href="index.php?action=forum">Forum</a>
                </li>
                <li class="nav-top__list-item nav-top__list-item--search">
                    <form class="search" action="index.php" method="GET">
                        <input type="search" id="search" name="search" autocomplete="off" placeholder="Find Games...">
                        <button type="submit"><i class="fa fa-search" aria-hidden="true"></i></button>
                    </form>
                </li>
                <?php
                if ($loggedIn) {
                    $accessStrength = $userType->getAccessStrength();
                    $_SESSION['AccessStrength'] = $accessStrength;
                    // Normal user components
                    if ($accessStrength >= UserType::User()->getAccessStrength()) {
                ?>
                        <li class="nav-top__list-item nav-top__list-item--logout inverted">
                            <a class="button--secondary" href="index.php?action=logout">Logout</a>
                        </li>
                    <?php
                    }
                    // Game creator components
                    if ($accessStrength >= UserType::Creator()->getAccessStrength()) {
                    ?>
                        <li class="nav-top__list-item nav-top__list-item--your-games">
                            <a class="button--secondary" href="index.php?action=listCreatedGames">Your Games</a>
                        </li>
                    <?php
                    }
                    // Admin components
                    if ($accessStrength >= UserType::Admin()->getAccessStrength()) {
                    }
                } else {
                    // if someone isn´t logged in
                    ?>
                    <li class="nav-top__list-item nav-top__list-item--login">
                        <a class="button--secondary" href="index.php?action=login">Login</a>
                    </li>
                    <li class="nav-top__list-item nav-top__list-item--register">
                        <a class="button--secondary" href="index.php?action=register">Register</a>
                    </li>
                <?php
                }
                ?>
            </ul>
        </nav>
        <?php
    }
}
// INIT COMPONENT
NavigationComponent::$instance = new NavigationComponent();
