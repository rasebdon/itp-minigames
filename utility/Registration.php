<?php
if (isset($_GET['action'])) {
    if ($_GET['action'] == 'register') {
?>
        <h1>Register</h1>
        <form action="" method="post">
            <div class="form-row">
                <div class="form-group col-6">
                    <label for="Email">Email</label>
                    <input type="email" class="form-control" name="Email" id="" aria-describedby="emailHelpId" placeholder="" value="<?= $_SESSION['register']['Email'] ?? "" ?>">
                    <small id="emailHelpId" class="form-text text-muted"><?= $_SESSION['registerErrors']['Email'] ?? '' ?></small>
                </div>
                <div class="form-group col-6">
                    <label for="Username">Username</label>
                    <input type="text" class="form-control" name="Username" id="" aria-describedby="helpId" placeholder="" value="<?= $_SESSION['register']['Username'] ?? "" ?>">
                    <small id="helpId" class="form-text text-muted"><?= $_SESSION['registerErrors']['Username'] ?? '' ?></small>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-6">
                    <label for="FirstName">FirstName</label>
                    <input type="text" class="form-control" name="FirstName" id="" aria-describedby="helpId" placeholder="" value="<?= $_SESSION['register']['FirstName'] ?? "" ?>">
                    <small id="helpId" class="form-text text-muted"><?= $_SESSION['registerErrors']['FirstName'] ?? '' ?></small>
                </div>
                <div class="form-group col-6">
                    <label for="LastName">LastName</label>
                    <input type="text" class="form-control" name="LastName" id="" aria-describedby="helpId" placeholder="" value="<?= $_SESSION['register']['LastName'] ?? "" ?>">
                    <small id="helpId" class="form-text text-muted"><?= $_SESSION['registerErrors']['LastName'] ?? '' ?></small>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-6">
                    <label for="Password">Password</label>
                    <input type="password" class="form-control" name="Password" id="" placeholder="" value="<?= $_SESSION['register']['Password'] ?? "" ?>">
                    <small id="helpId" class="form-text text-muted"><?= $_SESSION['registerErrors']['Password'] ?? '' ?></small>
                </div>
                <div class="form-group col-6">
                    <label for="ConfirmPassword">ConfirmPassword</label>
                    <input type="password" class="form-control" name="ConfirmPassword" id="" placeholder="" value="<?= $_SESSION['register']['ConfirmPassword'] ?? "" ?>">
                    <small id="helpId" class="form-text text-muted"><?= $_SESSION['registerErrors']['ConfirmPassword'] ?? '' ?></small>
                </div>
            </div>
            <button type="submit" name="RegisterSubmit" class="btn btn-primary">Submit</button>
        </form>
<?php
    }
}
?>