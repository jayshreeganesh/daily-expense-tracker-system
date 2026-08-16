<?php require_once APPROOT . '/views/layouts/header.php'; ?>

<div class="container mt-4" x-data="{ showModal: false }">
    <?php flash('transaction_message'); ?>

    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center mb-4 gap-3">
        <h2>Transactions</h2>
        <div class="d-flex flex-column flex-md-row align-items-stretch align-items-md-center gap-2">
            <form action="<?= URLROOT ?>/transaction/index" method="get" class="d-flex">
                <input type="month" name="month" class="form-control me-2" value="<?= $data['month'] ?? '' ?>" onchange="this.form.submit()">
                <?php if(!empty($data['month'])): ?>
                    <a href="<?= URLROOT ?>/transaction" class="btn btn-outline-secondary">Clear</a>
                <?php endif; ?>
            </form>
            <a href="<?= URLROOT ?>/transaction/export" class="btn btn-outline-success">Export CSV</a>
            <a href="<?= URLROOT ?>/transaction/exportPdf<?= !empty($data['month']) ? '?month=' . $data['month'] : '' ?>" class="btn btn-outline-danger">Export PDF</a>
            <button @click="showModal = true" class="btn btn-primary">+ Add Transaction</button>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Category</th>
                            <th>Amount</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($data['transactions'])) : ?>
                            <tr><td colspan="5" class="text-center text-muted p-4">No transactions found.</td></tr>
                        <?php else : ?>
                            <?php foreach($data['transactions'] as $tx) : ?>
                                <tr>
                                    <td><?= date('M d, Y', strtotime($tx->transaction_date)) ?></td>
                                    <td><?= htmlspecialchars($tx->description) ?></td>
                                    <td>
                                        <span class="badge rounded-pill" style="background-color: <?= $tx->color_code ?>;">
                                            <?= htmlspecialchars($tx->category_name) ?>
                                        </span>
                                    </td>
                                    <td class="fw-bold <?= $tx->type == 'income' ? 'text-success' : 'text-danger' ?>">
                                        <div><?= $tx->type == 'income' ? '+' : '-' ?><?= CURRENCY_SYMBOL ?><?= number_format($tx->amount, 2) ?></div>
                                        <?php if(isset($tx->original_currency) && $tx->exchange_rate != 1.0000 && $tx->original_amount != $tx->amount): ?>
                                            <small class="text-muted fw-normal">(<?= $tx->original_currency ?> <?= number_format($tx->original_amount, 2) ?>)</small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <form action="<?= URLROOT; ?>/transaction/delete/<?= $tx->id ?>" method="post" class="d-inline">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this transaction?');">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if($data['totalPages'] > 1): ?>
                <div class="p-3 border-top d-flex justify-content-center">
                    <ul class="pagination mb-0">
                        <?php for($i = 1; $i <= $data['totalPages']; $i++): ?>
                            <li class="page-item <?= $data['page'] == $i ? 'active' : '' ?>">
                                <a class="page-link" href="<?= URLROOT ?>/transaction/index?page=<?= $i ?><?= !empty($data['month']) ? '&month='.$data['month'] : '' ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
        </div>
    </div>

    <!-- AlpineJS Modal for Add Transaction -->
    <div :class="{ 'd-block': showModal }" class="modal" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow">
                <div class="modal-header bg-light">
                    <h5 class="modal-title">Add New Transaction</h5>
                    <button type="button" class="btn-close" @click="showModal = false"></button>
                </div>
                <form action="<?= URLROOT; ?>/transaction/add" method="post">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Type</label>
                            <select name="type" class="form-select" required>
                                <option value="expense">Expense</option>
                                <option value="income">Income</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <select name="category_id" class="form-select" required>
                                <option value="">Select Category</option>
                                <?php foreach($data['categories'] as $cat) : ?>
                                    <option value="<?= $cat->id ?>"><?= htmlspecialchars($cat->name) ?> (<?= ucfirst($cat->type) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Amount & Currency</label>
                            <div class="input-group">
                                <select name="currency" class="form-select" style="max-width: 100px;">
                                    <option value="USD" <?= CURRENCY_SYMBOL == '$' ? 'selected' : '' ?>>USD</option>
                                    <option value="EUR" <?= CURRENCY_SYMBOL == '€' ? 'selected' : '' ?>>EUR</option>
                                    <option value="GBP" <?= CURRENCY_SYMBOL == '£' ? 'selected' : '' ?>>GBP</option>
                                    <option value="INR" <?= CURRENCY_SYMBOL == '₹' ? 'selected' : '' ?>>INR</option>
                                    <option value="JPY" <?= CURRENCY_SYMBOL == '¥' ? 'selected' : '' ?>>JPY</option>
                                    <option value="CAD">CAD</option>
                                    <option value="AUD">AUD</option>
                                </select>
                                <input type="number" step="0.01" name="amount" class="form-control" required placeholder="0.00">
                            </div>
                            <small class="text-muted mt-1 d-block">Live exchange rates will be applied if currency differs from system default.</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Date</label>
                            <input type="date" name="transaction_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description (Optional)</label>
                            <textarea name="description" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="showModal = false">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Transaction</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once APPROOT . '/views/layouts/footer.php'; ?>
