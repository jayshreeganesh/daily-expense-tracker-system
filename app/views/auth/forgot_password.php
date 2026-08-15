<?php require_once APPROOT . '/views/layouts/header.php'; ?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-6 mx-auto">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white text-center py-3">
                    <h3 class="mb-0">Password Recovery</h3>
                </div>
                <div class="card-body p-4">
                    <?php flash('reset_message'); ?>
                    <p class="text-muted text-center mb-4">Enter your registered email address below and we'll send you a password reset link.</p>
                    <form action="<?= URLROOT; ?>/auth/forgotPassword" method="post">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address <sup>*</sup></label>
                            <input type="email" name="email" class="form-control form-control-lg <?= (!empty($data['email_err'])) ? 'is-invalid' : ''; ?>" value="<?= $data['email']; ?>" required>
                            <span class="invalid-feedback"><?= $data['email_err']; ?></span>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">Send Reset Link</button>
                        </div>
                    </form>
                </div>
                <div class="card-footer bg-light text-center py-3">
                    <a href="<?= URLROOT; ?>/auth/login" class="text-decoration-none">Back to Login</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once APPROOT . '/views/layouts/footer.php'; ?>
