<?php require_once APPROOT . '/views/layouts/header.php'; ?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-6 mx-auto">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark text-center py-3">
                    <h3 class="mb-0">Create New Password</h3>
                </div>
                <div class="card-body p-4">
                    <p class="text-muted text-center mb-4">Please enter your new secure password below.</p>
                    <form action="<?= URLROOT; ?>/auth/resetPassword" method="post">
                        <input type="hidden" name="email" value="<?= htmlspecialchars($data['email']) ?>">
                        <input type="hidden" name="token" value="<?= htmlspecialchars($data['token']) ?>">
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">New Password <sup>*</sup></label>
                            <input type="password" name="password" class="form-control form-control-lg <?= (!empty($data['password_err'])) ? 'is-invalid' : ''; ?>" required>
                            <span class="invalid-feedback"><?= $data['password_err']; ?></span>
                        </div>
                        <div class="mb-4">
                            <label for="confirm_password" class="form-label">Confirm Password <sup>*</sup></label>
                            <input type="password" name="confirm_password" class="form-control form-control-lg <?= (!empty($data['confirm_err'])) ? 'is-invalid' : ''; ?>" required>
                            <span class="invalid-feedback"><?= $data['confirm_err']; ?></span>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-warning btn-lg fw-bold">Update Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once APPROOT . '/views/layouts/footer.php'; ?>
