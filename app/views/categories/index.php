<?php require_once '../app/views/layouts/header.php'; ?>

<div class="container mt-4" x-data="{ showModal: false }">
    <?php flash('category_message'); ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Manage Categories</h2>
        <button @click="showModal = true" class="btn btn-primary">+ Add Category</button>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Color</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($data['categories'])) : ?>
                            <tr><td colspan="3" class="text-center text-muted p-4">No categories created yet.</td></tr>
                        <?php else : ?>
                            <?php foreach($data['categories'] as $cat) : ?>
                                <tr>
                                    <td><?= htmlspecialchars($cat->name) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $cat->type == 'income' ? 'success' : 'danger' ?>">
                                            <?= ucfirst($cat->type) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div style="width: 20px; height: 20px; border-radius: 50%; background-color: <?= $cat->color_code ?>;"></div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- AlpineJS Modal for Add Category -->
    <div x-show="showModal" style="display: none;" class="modal d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow">
                <div class="modal-header bg-light">
                    <h5 class="modal-title">Add New Category</h5>
                    <button type="button" class="btn-close" @click="showModal = false"></button>
                </div>
                <form action="<?= URLROOT; ?>/categorycontroller/add" method="post">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Category Name</label>
                            <input type="text" name="name" class="form-control" required placeholder="e.g. Salary, Rent, Groceries">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Type</label>
                            <select name="type" class="form-select" required>
                                <option value="expense">Expense</option>
                                <option value="income">Income</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Color Code</label>
                            <input type="color" name="color_code" class="form-control form-control-color" value="#0d6efd" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="showModal = false">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?>
