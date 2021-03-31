<?php
if (isset($_GET['action'])) {
    if ($_GET['action'] == 'register') {
?>
        <h1>Registration</h1>

        <form action="" method="POST">


            <div class="row">
                <div class="col-md-4">
                    <label for="FirstName">Firstname:</label>
                </div>
                <div class="col-md-8">
                    <input type="text" class="form-control" name="FirstName" />
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <label for="LastName">Lastname:</label>
                </div>
                <div class="col-md-8">
                    <input type="text" class="form-control" name="LastName" />
                </div>
            </div>


            <div class="row">
                <div class="col-md-4">
                    <label for="Username">Username:</label>
                </div>
                <div class="col-md-8">
                    <input type="text" class="form-control" name="Username">
                </div>
            </div>


            <div class="row">
                <div class="col-md-4">
                    <label for="Email">E-Mail:</label>
                </div>
                <div class="col-md-8">
                    <input type="email" class="form-control" placeholder="example@mail.com" name="Email" />
                </div>
            </div>


            <div class="row">
                <div class="col-md-4">
                    <label for="Password">Password:</label>
                </div>
                <div class="col-md-8">
                    <input type="password" class="form-control" placeholder="password" name="Password" />
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <label for="PasswordRep">Repeat password:</label>
                </div>
                <div class="col-md-8">
                    <input type="password" class="form-control" placeholder="repeat password" name="PasswordRep" />
                </div>
            </div>


            <div class="senden">
                <label for="submit">
                    <input type="submit" name="registerSubmit" value="Send" />
                </label>
                <label for="reset">
                    <input type="reset" name="reset" value="Cancel" name="reset" />
                </label>
            </div>
        </form>
<?php
    }
}
?>