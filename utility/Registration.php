<?php
if (isset($_GET['action'])) {
    if ($_GET['action'] == 'register') {
?>
        <section class="register">
            <h2 class="heading-secondary">Register</h2>
            <div class="register__form">
                <form class="form" method="post">
                    <div class="form__group">
                        <input class="form__input" id="registerEmail" type="email" name="Email" placeholder="Email" value="<?= $_SESSION['register']['Email'] ?? "" ?>">
                        <label class="form__label" for="registerEmail">Email</label>
                        <span class="form__separator"></span>
                    </div>
                    <small><?= $_SESSION['registerErrors']['Email'] ?? '' ?></small>
                    <div class="form__group">
                        <input class="form__input" id="registerUsername" type="text" name="Username" placeholder="Username" value="<?= $_SESSION['register']['Username'] ?? "" ?>">
                        <label class="form__label" for="registerUsername">Username</label>
                        <span class="form__separator"></span>
                    </div>
                    <small><?= $_SESSION['registerErrors']['Username'] ?? '' ?></small>
                    <div class="form__group">
                        <input class="form__input" id="registerFirstName" type="text" name="FirstName" placeholder="First Name" value="<?= $_SESSION['register']['FirstName'] ?? "" ?>">
                        <label class="form__label" for="registerFirstName">First Name</label>
                        <span class="form__separator"></span>
                    </div>
                    <small><?= $_SESSION['registerErrors']['FirstName'] ?? '' ?></small>
                    <div class="form__group">
                        <input class="form__input" id="registerLastName" type="text" name="LastName" placeholder="Last Name" value="<?= $_SESSION['register']['LastName'] ?? "" ?>">
                        <label class="form__label" for="registerLastName">Last Name</label>
                        <span class="form__separator"></span>
                    </div>
                    <small><?= $_SESSION['registerErrors']['LastName'] ?? '' ?></small>
                    <div class="form__group">
                        <input class="form__input" id="registerPassword" type="password" name="Password" placeholder="Password" value="<?= $_SESSION['register']['Password'] ?? "" ?>">
                        <label class="form__label" for="registerPassword">Password</label>
                        <span class="form__separator"></span>
                    </div>
                    <small><?= $_SESSION['registerErrors']['Password'] ?? '' ?></small>
                    <div class="form__group">
                        <input class="form__input" id="registerConfirmPassword" type="password" name="ConfirmPassword" placeholder="Confirm Password" value="<?= $_SESSION['register']['ConfirmPassword'] ?? "" ?>">
                        <label class="form__label" for="registerConfirmPassword">Confirm Password</label>
                        <span class="form__separator"></span>
                    </div>
                    <small><?= $_SESSION['registerErrors']['ConfirmPassword'] ?? '' ?></small>
                    <button class="button button--primary" type="submit" name="RegisterSubmit">Submit</button>
                </form>
            </div>
        </section>
<?php
    }
}
?>