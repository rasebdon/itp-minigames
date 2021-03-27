<?php
class UserAdministration {
    
    /**
     * Shows the next set of users from the old starting points:
     * * If forward is true -> The next $amount of users will be loaded,
     * beginning from the old last index
     * * If forward is false -> The last $amount of users will be loaded,
     * beginning from the old first index
     */
    public static function ShowUsers($position = 0, $loaded = 0, $amount = 20) {
        // Check if users can be loaded (users amount > 0)
        if(($usersAmount = UserService::$instance->getUserCount()) == 0)
            return;

        // Set position and direction if not set
        if(!isset($position)) {
            $position = 0;
        }
        if(!isset($loaded))
            $loaded = 0;

        $oldLoaded = $loaded;
        $oldPosition = $position;
        $posMod = 0;
        ?>
        <div class="row user-administration-list">
            <div class="user-box col-12 row user-administration-list-item">
                <div class="col-3">ID</div>
                <div class="col-3">Username</div>
                <div class="col-3">First Name</div>
                <div class="col-3">Last Name</div>
            </div>
        <?php
       
        for ($i = $position; $loaded < $usersAmount && $loaded - $oldLoaded < $amount; $i++) { 
            if(($user = UserService::$instance->getUser($i)) != null) {
                ?>
                <div class="user-box col-12 row user-administration-list-item">
                    <div class="col-2"><?= $i ?></div>
                    <div class="col-2"><?= $user->getId()?></div>
                    <div class="col-2"><?= $user->getUsername() ?></div>
                    <div class="col-2"><?= $user->getLastName()?></div>
                    <div class="col-2"><?= $user->getFirstName()?></div>
                </div>
                <?php
                // Add one to loaded
                $loaded++;
            }
            $position++;
        }



        // Add buttons
        ?>
        <div class="mt-5 col-12 row p-0">
        <div class="mt-2 mb-2 col-12"><p>Showing users <?=$oldLoaded + 1?> - <?=$loaded?> ( <?=$usersAmount?> total )</p></div>
        <div class="d-flex col-6 justify-content-start p-0">
        <?php
        if($loaded > $amount) {
            // Add load last amount
            ?>
            <a type="button" class="btn btn-primary" href="/?action=showUsers&amount=<?= $amount ?>&position=<?=$oldPosition - ($position - $oldPosition)?>&loaded=<?=$oldLoaded - $amount >= 0 ? $oldLoaded - $amount : 0?>">
                Last <?= $amount ?> Users
            </a>
            <?php

        }
        ?>
        </div>
        <div class="d-flex col-6 justify-content-end p-0">
        <?php
        if($loaded < $usersAmount) {
            // Add load next amount
            ?>
            <a type="button" class="btn btn-primary" href="/?action=showUsers&amount=<?= $amount ?>&position=<?=$position?>&loaded=<?=$loaded?>">
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

}