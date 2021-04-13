<?php
if (isset($_GET['action'])) {
    if ($_GET['action'] == 'editProfile') {
?>

        <h1>Edit Profile</h1>
        <form action="" method="post">
            <div class="form-row">
                <div class="form-group col-6">
                    <label for="Username">Username</label>
                    <input type="text" class="form-control" name="Username" id="" aria-describedby="helpId" placeholder="" value="<?= $user->getUsername() ?>">
                    <small id="helpId" class="form-text text-muted"><?= $_SESSION['editProfileErrors']['Username'] ?? '' ?></small>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-6">
                    <label for="FirstName">FirstName</label>
                    <input type="text" class="form-control" name="FirstName" id="" aria-describedby="helpId" placeholder="" value="<?= $user->getFirstName() ?>">
                    <small id="helpId" class="form-text text-muted"><?= $_SESSION['editProfileErrors']['FirstName'] ?? '' ?></small>
                </div>
                <div class="form-group col-6">
                    <label for="LastName">LastName</label>
                    <input type="text" class="form-control" name="LastName" id="" aria-describedby="helpId" placeholder="" value="<?= $user->getLastName() ?>">
                    <small id="helpId" class="form-text text-muted"><?= $_SESSION['editProfileErrors']['LastName'] ?? '' ?></small>
                </div>
            </div>
            <button type="submit" name="SubmitSettings" class="btn btn-primary">Save</button>
        </form>

        <form action="" method="post">
            <div class="form-row">
                <div class="form-group col-6">
                    <label for="CurrentPassword">Current Password</label>
                    <input type="password" class="form-control" name="CurrentPassword" id="" placeholder="">
                    <small id="helpId" class="form-text text-muted"><?= $_SESSION['passwordErrors']['CurrentPassword'] ?? '' ?></small>
                </div>
                <div class="form-group col-6">
                    <label for="Password">Password</label>
                    <input type="password" class="form-control" name="Password" id="" placeholder="">
                    <small id="helpId" class="form-text text-muted"><?= $_SESSION['passwordErrors']['Password'] ?? '' ?></small>
                </div>
                <div class="form-group col-6">
                    <label for="ConfirmPassword">ConfirmPassword</label>
                    <input type="password" class="form-control" name="ConfirmPassword" id="" placeholder="">
                    <small id="helpId" class="form-text text-muted"><?= $_SESSION['passwordErrors']['ConfirmPassword'] ?? '' ?></small>
                </div>
            </div>
            <button type="submit" name="SubmitPassword" class="btn btn-primary">Save</button>
        </form>
<?php
    }
}
?>