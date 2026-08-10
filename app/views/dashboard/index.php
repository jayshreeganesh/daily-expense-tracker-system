<?php require_once '../app/views/layouts/header.php'; ?>

<div class="container" x-data="{ showWelcome: true }">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm mb-4" x-show="showWelcome" x-transition>
                <div class="card-body d-flex justify-content-between align-items-center">
                    <h2 class="card-title text-primary m-0">Welcome to <?= SITENAME ?></h2>
                    <button class="btn btn-sm btn-outline-secondary" @click="showWelcome = false">Dismiss</button>
                </div>
            </div>
            
            <p class="lead">Dashboard will be built here in Stage 4.</p>
        </div>
    </div>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?>
