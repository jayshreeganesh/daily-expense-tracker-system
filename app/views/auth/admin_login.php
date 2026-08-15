<?php require APPROOT . '/views/layouts/header.php'; ?>
<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="card card-body bg-light mt-5 border-danger shadow-sm">
            <h2 class="text-danger"><span class="text-danger">👑</span> Secure Admin Portal</h2>
            <p class="text-muted">Please fill in your credentials to access the Admin/Recruiter dashboard.</p>
            <form action="<?= URLROOT ?>/auth/adminLogin" method="post">
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
                <div class="row">
                    <div class="col">
                        <input type="submit" value="Login to Admin Panel" class="btn btn-danger w-100 shadow-sm">
                    </div>
                </div>
            </form>
            <div class="text-center mt-4">
                <a href="<?= URLROOT ?>/auth" class="text-decoration-none text-muted">← Back to Portal Selection</a>
            </div>
        </div>
    </div>
</div>
<?php require APPROOT . '/views/layouts/footer.php'; ?>
