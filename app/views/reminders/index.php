<?php require_once APPROOT . '/views/layouts/header.php'; ?>

<div class="container mt-4" x-data="{ showModal: false }">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>⏰ Bill Reminders</h2>
        <button @click="showModal = true" class="btn btn-primary">+ Add Reminder</button>
    </div>

    <div class="row">
        <?php if(empty($data['reminders'])) : ?>
            <p class="text-center text-muted">No reminders found. Stay on top of your bills!</p>
        <?php else : ?>
            <?php foreach($data['reminders'] as $reminder) : ?>
                <div class="col-md-4 mb-3">
                    <div class="card shadow-sm h-100 <?= $reminder->status == 'paid' ? 'bg-light border-success' : 'border-warning' ?>">
                        <div class="card-body">
                            <h5 class="card-title <?= $reminder->status == 'paid' ? 'text-decoration-line-through text-muted' : '' ?>"><?= htmlspecialchars($reminder->title) ?></h5>
                            <h6 class="card-subtitle mb-2 text-muted">Due: <?= date('M d, Y', strtotime($reminder->due_date)) ?></h6>
                            <p class="card-text fw-bold text-danger">Amount: <?= CURRENCY_SYMBOL ?><?= number_format($reminder->amount, 2) ?></p>
                        </div>
                        <div class="card-footer bg-transparent border-0 d-flex justify-content-between">
                            <?php if($reminder->status == 'pending'): ?>
                                <a href="<?= URLROOT ?>/reminder/complete/<?= $reminder->id ?>" class="btn btn-sm btn-outline-success">✔ Mark Paid</a>
                            <?php else: ?>
                                <span class="badge bg-success">Completed</span>
                            <?php endif; ?>
                            
                            <form action="<?= URLROOT ?>/reminder/delete/<?= $reminder->id ?>" method="post">
                                <button type="submit" class="btn btn-sm btn-outline-danger">🗑️</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- AlpineJS Modal for Add Reminder -->
    <div :class="{ 'd-block': showModal }" class="modal" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow">
                <div class="modal-header bg-light">
                    <h5 class="modal-title">Add New Reminder</h5>
                    <button type="button" class="btn-close" @click="showModal = false"></button>
                </div>
                <form action="<?= URLROOT ?>/reminder/add" method="post">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" class="form-control" required placeholder="e.g. Electric Bill">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Due Date</label>
                            <input type="date" name="due_date" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Amount</label>
                            <input type="number" step="0.01" name="amount" class="form-control" required placeholder="0.00">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="showModal = false">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Reminder</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once APPROOT . '/views/layouts/footer.php'; ?>
