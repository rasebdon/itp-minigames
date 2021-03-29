<?php
class UserAdministration {
    
    /** @var UserAdminstration */
    public static $instance;

    function __construct()
    {
        // Check for delete user
        if(isset($_GET['delete'])) {
            UserService::$instance->deleteUser($_GET['delete']);
        }
        // Route GET variables
        if(!isset($_GET['action']))
            return;
        switch($_GET['action']) {
            case "showUsers":
                if(isset($_GET['offset']) && isset($_GET['amount']))
                    $this->ShowUsers($_GET['offset'], $_GET['amount']);
                break;
            case "showUser":
                if(isset($_GET['id']))
                    $this->ShowUser($_GET['id']);
                break;
        }
    }

    /**
     * Shows the next set of users from the offset and amount given
     */
    public function ShowUsers($offset = 0, $amount = 20) {
        // Check if users can be loaded (users amount > 0)
        if(($usersAmount = UserService::$instance->getUserCount()) == 0)
            return;

        // Loaded important to know for offset management
        $loaded = 0;
        ?>
        <div class="row user-administration-list">
            <div class="mb-3 col-12 text-center">
            <h1 class="display-3">User Administration System</h1>
            </div>
            <div class="user-box col-12 row user-administration-list-item">
                <div class="col-2 fw-bold">ID</div>
                <div class="col-2 fw-bold">Username</div>
                <div class="col-2 fw-bold">First Name</div>
                <div class="col-2 fw-bold">Last Name</div>
                <div class="col-2 fw-bold">Role</div>
                <div class="col-1 fw-bold">Delete</div>
                <div class="col-1 fw-bold">View</div>
            </div>
        <?php

        // Load the users from the service class
        $users = UserService::$instance->getUsers($offset, $amount);

        // Render all users that were loaded
        foreach($users as $user) {
            ?>
            <div class="user-box col-12 row user-administration-list-item">
                <div class="col-2"><?= $user->getId()?></div>
                <div class="col-2"><?= $user->getUsername() ?></div>
                <div class="col-2"><?= $user->getLastName()?></div>
                <div class="col-2"><?= $user->getFirstName()?></div>
                <div class="col-2">user</div>
                <div class="col-1">
                    <a href="<?= "?action=showUsers&amount=$amount&offset=$offset&delete=" . $user->getId() ?>" class="btn btn-danger">X</a>
                </div>
                <div class="col-1">
                    <a href="<?= "?action=showUser&id=" . $user->getId() ?>" class="btn btn-primary"><i class="bi bi-search"></i></a>
                </div>
            </div>
            <?php
            $loaded++;
        }

        // Add buttons
        ?>
        <div class="mt-5 col-12 row p-0">
        <div class="mt-2 mb-2 col-12"><p>Showing users <?=$offset + 1?> - <?=$offset + $loaded?> ( <?=$usersAmount?> total )</p></div>
        <div class="d-flex col-6 justify-content-start p-0">
        <?php
        // If there are any users left that can be loaded before ( > amount ) then show button
        if($loaded + $offset > $amount) {
            // Add load last amount
            ?>
            <a type="button" class="btn btn-primary" href="/?action=showUsers&amount=<?= $amount ?>&offset=<?=$offset - $loaded > 0 ? $offset - $loaded : 0?>">
                Last <?= $amount ?> Users
            </a>
            <?php

        }
        ?>
        </div>
        <div class="d-flex col-6 justify-content-end p-0">
        <?php
        // If there are any users left that can be loaded after ( < total users in db ) then show button
        if($loaded + $offset < $usersAmount) {
            // Add load next amount
            ?>
            <a type="button" class="btn btn-primary" href="/?action=showUsers&amount=<?= $amount ?>&offset=<?= $offset + $loaded?>">
                Next <?= $amount ?> Users
            </a>
            <?php
        }
        ?>
        </div>
        </div>
        </div>
        <?php
    }

    public function ShowUser($uid) {
        // Get user or quit
        if(($user = UserService::$instance->getUser($uid)) === null) {
            return;
        }

        ?>
        <div class="row">
            <div class="col-12 mb-5">
                <h1 class="display-3">Detailed User View</h1>
            </div>
            <div class="col-12 row">
                <div class="col-2">
                    <p class="h1">ID:</p>
                </div>
                <div class="col-10">
                    <p class="h1"><?=$user->getId()?></p>
                </div>
            </div>
            <div class="col-12 row">
                <div class="col-2">
                    <p class="h1">Username:</p>
                </div>
                <div class="col-10">
                    <p class="h1"><?=$user->getUsername()?></p>
                </div>
            </div>
            <div class="col-12 row">
                <div class="col-2">
                    <p class="h1">Last name:</p>
                </div>
                <div class="col-10">
                    <p class="h1"><?=$user->getLastName()?></p>
                </div>
            </div>
            <div class="col-12 row">
                <div class="col-2">
                    <p class="h1">First name:</p>
                </div>
                <div class="col-10">
                    <p class="h1"><?=$user->getFirstName()?></p>
                </div>
            </div>
        </div>
        <?php
    }

}

UserAdministration::$instance = new UserAdministration();