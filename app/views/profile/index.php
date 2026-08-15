<?php require_once APPROOT . '/views/layouts/header.php'; ?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <?php flash('profile_message'); ?>
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h4 class="mb-0">Profile Settings</h4>
                </div>
                <div class="card-body">
                    <form action="<?= URLROOT; ?>/profile/index" method="post">
                        <div class="mb-3">
                            <label>Name: <sup>*</sup></label>
                            <input type="text" name="name" class="form-control <?= (!empty($data['name_err'])) ? 'is-invalid' : ''; ?>" value="<?= $data['user']->name; ?>">
                            <span class="invalid-feedback"><?= $data['name_err']; ?></span>
                        </div>
                        <div class="mb-3">
                            <label>Email: <sup>*</sup></label>
                            <input type="email" name="email" class="form-control <?= (!empty($data['email_err'])) ? 'is-invalid' : ''; ?>" value="<?= $data['user']->email; ?>">
                            <span class="invalid-feedback"><?= $data['email_err']; ?></span>
                        </div>
                        <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h4 class="mb-0">Change Password</h4>
                </div>
                <div class="card-body">
                    <form action="<?= URLROOT; ?>/profile/index" method="post">
                        <div class="mb-3">
                            <label>New Password: <sup>*</sup></label>
                            <input type="password" name="password" class="form-control <?= (!empty($data['password_err'])) ? 'is-invalid' : ''; ?>" value="">
                            <span class="invalid-feedback"><?= $data['password_err']; ?></span>
                        </div>
                        <div class="mb-3">
                            <label>Confirm Password: <sup>*</sup></label>
                            <input type="password" name="confirm_password" class="form-control <?= (!empty($data['confirm_password_err'])) ? 'is-invalid' : ''; ?>" value="">
                            <span class="invalid-feedback"><?= $data['confirm_password_err']; ?></span>
                        </div>
                        <button type="submit" name="update_password" class="btn btn-warning">Change Password</button>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm border-danger">
                <div class="card-header bg-danger text-white">
                    <h4 class="mb-0">Danger Zone</h4>
                </div>
                <div class="card-body">
                    <p class="text-muted">Deleting your account is permanent. All your transactions, categories, and reminders will be erased forever.</p>
                    <form action="<?= URLROOT; ?>/profile/index" method="post">
                        <button type="submit" name="delete_account" class="btn btn-outline-danger" onclick="return confirm('Are you completely sure you want to delete your account? This action cannot be undone.');">Delete My Account</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once APPROOT . '/views/layouts/footer.php'; ?>
