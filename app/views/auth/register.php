<?php require_once APPROOT . '/views/layouts/header.php'; ?>
<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="card card-body bg-light mt-5 mb-5">
            <h2>Create An Account</h2>
            <p>Please fill out this form to register</p>
            <form action="<?= URLROOT; ?>/auth/register" method="post">
                <div class="mb-3">
                    <label for="name" class="form-label">Name: <sup>*</sup></label>
                    <input type="text" name="name" class="form-control <?= (!empty($data['name_err'])) ? 'is-invalid' : ''; ?>" value="<?= $data['name'] ?? ''; ?>">
                    <span class="invalid-feedback"><?= $data['name_err'] ?? ''; ?></span>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email: <sup>*</sup></label>
                    <input type="email" name="email" class="form-control <?= (!empty($data['email_err'])) ? 'is-invalid' : ''; ?>" value="<?= $data['email'] ?? ''; ?>">
                    <span class="invalid-feedback"><?= $data['email_err'] ?? ''; ?></span>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password: <sup>*</sup></label>
                    <input type="password" name="password" class="form-control <?= (!empty($data['password_err'])) ? 'is-invalid' : ''; ?>">
                    <span class="invalid-feedback"><?= $data['password_err'] ?? ''; ?></span>
                </div>
                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirm Password: <sup>*</sup></label>
                    <input type="password" name="confirm_password" class="form-control <?= (!empty($data['confirm_password_err'])) ? 'is-invalid' : ''; ?>">
                    <span class="invalid-feedback"><?= $data['confirm_password_err'] ?? ''; ?></span>
                </div>
                <div class="row">
                    <div class="col">
                        <input type="submit" value="Register" class="btn btn-primary w-100">
                    </div>
                    <div class="col">
                        <a href="<?= URLROOT; ?>/auth/login" class="btn btn-light w-100">Have an account? Login</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php require_once APPROOT . '/views/layouts/footer.php'; ?>
