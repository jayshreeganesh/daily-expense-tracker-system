<?php require APPROOT . '/views/layouts/header.php'; ?>
<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="card card-body bg-light mt-5 text-center shadow-sm">
            <h2 class="mb-3">Welcome to Expense Tracker</h2>
            <p class="text-muted">Please select your portal to continue.</p>
            <div class="mt-4">
                <a href="<?= URLROOT ?>/auth/login" class="btn btn-primary btn-lg w-100 mb-3 shadow-sm">
                    👤 User Login Portal
                </a>
                <a href="<?= URLROOT ?>/auth/adminLogin" class="btn btn-danger btn-lg w-100 mb-3 shadow-sm">
                    👑 Admin / Recruiter Portal
                </a>
                <hr>
                <p class="mt-3">New user? <a href="<?= URLROOT ?>/auth/register" class="text-decoration-none">Register here</a></p>
            </div>
        </div>
    </div>
</div>
<?php require APPROOT . '/views/layouts/footer.php'; ?>
