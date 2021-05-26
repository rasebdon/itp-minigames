<?php
if (isset($_GET['action'])) {
    if ($_GET['action'] == 'editProfile') {
?>
        <section class="edit-profile">
            <h2 class="heading-secondary">Edit Profile</h2>
            <div class="edit-profile__profile-picture">
                <h3 class="heading-tertiary">Profile Picture</h3>
                <div class="row">
                    <div class="col-12 col-lg-9">
                        <div id="cropProfilePicture" class="crop">
                            <label for="profilePictureUpload" class="file__label">
                                Upload image to crop...
                            </label>
                            <input type="file" class="crop__input file" accept="image/*" name="crop-input" id="profilePictureUpload">
                            <div class="crop__cropper-container">
                                <img class="crop__cropper-image" data-src="" alt="">
                                <img class="crop__cropper-image-clipped" alt="">
                                <div class="crop__cropper">
                                    <span class="crop__crophandle crop__crophandle--tl"></span>
                                    <span class="crop__crophandle crop__crophandle--tr"></span>
                                    <span class="crop__crophandle crop__crophandle--br"></span>
                                    <span class="crop__crophandle crop__crophandle--bl"></span>
                                </div>
                                <div class="crop__crop-overlay"></div>
                            </div>
                            <form method="POST" enctype="multipart/form-data" class="crop__form">
                                <input type="hidden" name="ProfilePictureSubmit" value="">
                                <button type="button" class="crop__submit button button--primary">Crop</button>
                            </form>
                        </div>
                        <script src="scripts/js/profilePictureCropper.js"></script>
                    </div>
                    <div class="col-12 col-lg-3 d-flex justify-content-center align-items-center">
                        <img class="profile-picture--contain" src="<?= ProfilePictureService::$instance->getPicture($user->getFK_PictureID())->getThumbnailPath() ?>" alt="profilePicture">
                    </div>
                </div>
            </div>

            <div class="edit-profile__change-data">
                <h3 class="heading-tertiary">Profile Data</h3>
                <form class="form" method="post">
                    <div class="form__group">
                        <input type="text" class="form__input" id="editUsername" name="Username" placeholder="Username" value="<?= $user->getUsername() ?>">
                        <label class="form__label" for="editUsername">Username</label>
                        <span class="form__separator"></span>
                    </div>
                    <small class="form-text text-muted"><?= $_SESSION['editProfileErrors']['Username'] ?? '' ?></small>
                    <div class="form__group">
                        <input type="text" class="form__input" id="editFirstName" name="FirstName" placeholder="First Name" value="<?= $user->getFirstName() ?>">
                        <label class="form__label" for="editFirstName">First Name</label>
                        <span class="form__separator"></span>
                    </div>
                    <small class="form-text text-muted"><?= $_SESSION['editProfileErrors']['FirstName'] ?? '' ?></small>
                    <div class="form__group">
                        <input type="text" class="form__input" id="editLastName" name="LastName" placeholder="Last Name" value="<?= $user->getLastName() ?>">
                        <label class="form__label" for="editLastName">Last Name</label>
                        <span class="form__separator"></span>
                    </div>
                    <small class="form-text text-muted"><?= $_SESSION['editProfileErrors']['LastName'] ?? '' ?></small>
                    <button type="submit" name="SubmitSettings" class="button button--primary">Save</button>
                </form>
            </div>

            <div class="edit-profile__change-password">
                <h3 class="heading-tertiary">Password</h3>
                <form class="form" method="post">
                    <div class="form__group">
                        <input type="password" class="form__input" id="editCurrentPassword" name="CurrentPassword" placeholder="Current Password">
                        <label class="form__label" for="editCurrentPassword">Current Password</label>
                        <span class="form__separator"></span>
                    </div>
                    <small class="form-text text-muted"><?= $_SESSION['passwordErrors']['CurrentPassword'] ?? '' ?></small>
                    <div class="form__group">
                        <input type="password" class="form__input" id="editPassword" name="Password" placeholder="Password">
                        <label class="form__label" for="editPassword">Password</label>
                        <span class="form__separator"></span>
                    </div>
                    <small class="form-text text-muted"><?= $_SESSION['passwordErrors']['Password'] ?? '' ?></small>
                    <div class="form__group">
                        <input type="password" class="form__input" id="editConfirmPassword" name="ConfirmPassword" placeholder="Confirm Password">
                        <label class="form__label" for="editConfirmPassword">ConfirmPassword</label>
                        <span class="form__separator"></span>
                    </div>
                    <small class="form-text text-muted"><?= $_SESSION['passwordErrors']['ConfirmPassword'] ?? '' ?></small>
                    <button type="submit" name="SubmitPassword" class="button button--primary">Save</button>
                </form>
            </div>
        </section>
<?php
    }
}
?>