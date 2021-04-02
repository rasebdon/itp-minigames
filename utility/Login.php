<?php
if (isset($_GET['action'])) {
    if ($_GET['action'] == 'login') {
?>
        <h1>Login</h1>
        <form action="" method="POST">

            <div class="form-group">
                <label for="Username"></label>
                <input type="text" class="form-control" name="Username" id="" aria-describedby="helpId" placeholder="username">
                <small id="helpId" class="form-text text-muted"><?= $_SESSION['loginErrors']['Username'] ?? '' ?></small>
            </div>

            <div class="form-group">
                <label for="Password"></label>
                <input type="password" class="form-control" name="Password" id="" placeholder="password">
                <small id="helpId" class="form-text text-muted"><?= $_SESSION['loginErrors']['Password'] ?? '' ?></small>
            </div>

            <button type="submit" name="LoginSubmit" class="btn btn-primary">login</button>
        </form>

<?php
    }
}
