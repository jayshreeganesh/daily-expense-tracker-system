<?php require_once APPROOT . '/views/layouts/header.php'; ?>
<div class="container mt-5 text-center">
    <h1 class="display-4 text-success">🎉 Subscription Successful!</h1>
    <p class="lead mt-3">Welcome to Premium! Your account has been upgraded.</p>
    <p class="text-muted">It might take a few moments for the webhook to update your status. If you don't see premium features unlocked immediately, please log out and back in.</p>
    <a href="<?= URLROOT ?>/dashboard" class="btn btn-primary mt-4">Go to Dashboard</a>
</div>
<?php require_once APPROOT . '/views/layouts/footer.php'; ?>
