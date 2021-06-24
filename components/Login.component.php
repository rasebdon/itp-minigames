<?php
class LoginComponent
{
    /** @var LoginComponent */
    public static $instance;
    function __construct()
    {
        if (isset($_GET['action']) && $_GET['action'] == 'login') {
            $this->showLogin();
        }
    }

    function showLogin() {
        ?>
        <section class="login">
            <h2 class="heading-secondary">Login</h2>
            <div class="login__form">
                <form class="form" method="POST">
                    <div class="form__group">
                        <input type="text" class="form__input" name="Username" id="loginUsername" placeholder="Username">
                        <label class="form__label" for="loginUsername">Username</label>
                        <span class="form__separator"></span>
                    </div>

                    <div class="form__group">
                        <input type="password" class="form__input" name="Password" id="loginPassword" placeholder="Password">
                        <label class="form__label" for="loginPassword">Password</label>
                        <span class="form__separator"></span>
                    </div>
                    <small class="form-text text-muted"><?= $_SESSION['loginErrors']['Password'] ?? '' ?></small>

                    <div>
                        <input class="checkbox" type="checkbox" id="loginRemember" name="rememberme">
                        <label for="loginRemember">Remember me?</label>
                    </div>

                    <button type="submit" name="LoginSubmit" class="button button--primary">Login</button>
                </form>
            </div>
        </section>
    <?php
    }
}

// INIT COMPONENT
LoginComponent::$instance = new LoginComponent();