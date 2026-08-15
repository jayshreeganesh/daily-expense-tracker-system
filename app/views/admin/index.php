<?php require_once APPROOT . '/views/layouts/header.php'; ?>

<div class="container mt-4" x-data="{ showImportModal: false, showUserModal: false }">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><span class="text-info">⚙️</span> Super Admin Dashboard</h2>
        <div>
            <?php if($_SESSION['user_role'] === 'admin'): ?>
                <a href="<?= URLROOT ?>/admin/seedData" class="btn btn-secondary shadow-sm me-1">🌱 Seed Data</a>
                <a href="<?= URLROOT ?>/admin/createZip?v=<?= time() ?>" class="btn btn-dark shadow-sm me-1">📦 Export Project</a>
            <?php endif; ?>
            <button @click="showUserModal = true" class="btn btn-success shadow-sm me-1">👤 Add User</button>
            <button @click="showImportModal = true" class="btn btn-primary shadow-sm me-1">📤 Import CSV</button>
            <?php if($_SESSION['user_role'] === 'admin'): ?>
                <a href="<?= URLROOT ?>/admin/backup?v=<?= time() ?>" class="btn btn-warning shadow-sm">📥 Backup DB</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title">Total Users</h5>
                    <h2 class="display-5"><?= $data['stats']['users'] ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title">Total Transactions</h5>
                    <h2 class="display-5"><?= $data['stats']['transactions'] ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-info text-white shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title">Platform Total Income Flow</h5>
                    <h2 class="display-5"><?= CURRENCY_SYMBOL ?><?= number_format($data['stats']['total_money'], 2) ?></h2>
                </div>
            </div>
        </div>
    </div>

    <!-- User Management -->
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">User Management</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Registered</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($data['users'] as $user) : ?>
                            <tr>
                                <td><?= $user->id ?></td>
                                <td><?= $user->name ?></td>
                                <td><?= $user->email ?></td>
                                <td>
                                    <?php if($user->role === 'admin'): ?>
                                        <span class="badge bg-danger">Admin</span>
                                    <?php elseif($user->role === 'recruiter'): ?>
                                        <span class="badge bg-info">Recruiter</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">User</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= date('Y-m-d', strtotime($user->created_at)) ?></td>
                                <td>
                                    <?php if($_SESSION['user_role'] === 'admin' && $user->id != $_SESSION['user_id']): ?>
                                        <form action="<?= URLROOT ?>/admin/updateRole" method="post" class="d-flex align-items-center mb-0">
                                            <input type="hidden" name="user_id" value="<?= $user->id ?>">
                                            <select name="role" class="form-select form-select-sm me-2" style="width: auto;">
                                                <option value="user" <?= $user->role == 'user' ? 'selected' : '' ?>>User</option>
                                                <option value="recruiter" <?= $user->role == 'recruiter' ? 'selected' : '' ?>>Recruiter</option>
                                                <option value="admin" <?= $user->role == 'admin' ? 'selected' : '' ?>>Admin</option>
                                            </select>
                                            <button type="submit" class="btn btn-sm btn-primary">Save</button>
                                        </form>
                                    <?php else: ?>
                                        <button class="btn btn-sm btn-outline-secondary" disabled>Edit Locked</button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Audit Logs -->
    <div class="card shadow-sm mt-4 mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0">System Audit Logs</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Time</th>
                            <th>User</th>
                            <th>Action</th>
                            <th>Entity Type</th>
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($data['auditLogs'])): ?>
                            <tr><td colspan="5" class="text-center p-3">No audit logs recorded yet.</td></tr>
                        <?php else: ?>
                            <?php foreach($data['auditLogs'] as $log) : ?>
                                <tr>
                                    <td><small><?= date('Y-m-d H:i:s', strtotime($log->created_at)) ?></small></td>
                                    <td><?= $log->user_name ?></td>
                                    <td><strong><?= $log->action ?></strong></td>
                                    <td><?= $log->entity_type ?> #<?= $log->entity_id ?></td>
                                    <td><small><?= $log->details ?></small></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- AlpineJS Modal for Add User -->
    <div :class="{ 'd-block': showUserModal }" class="modal" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow">
                <div class="modal-header bg-light">
                    <h5 class="modal-title">Create New User</h5>
                    <button type="button" class="btn-close" @click="showUserModal = false"></button>
                </div>
                <form action="<?= URLROOT ?>/admin/createUser" method="post">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <select name="role" class="form-select" required>
                                <option value="user">Standard User</option>
                                <option value="recruiter">Recruiter (Read-Only Admin)</option>
                                <option value="admin">Super Admin</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="showUserModal = false">Cancel</button>
                        <button type="submit" class="btn btn-success">Create User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- AlpineJS Modal for Import CSV -->
    <div :class="{ 'd-block': showImportModal }" class="modal" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow">
                <div class="modal-header bg-light">
                    <h5 class="modal-title">Bulk Import Transactions</h5>
                    <button type="button" class="btn-close" @click="showImportModal = false"></button>
                </div>
                <form action="<?= URLROOT ?>/admin/importTransactions" method="post" enctype="multipart/form-data">
                    <div class="modal-body">
                        <p class="text-muted small">Upload a CSV file to bulk import transactions for any user.</p>
                        <div class="mb-3">
                            <label class="form-label">Select CSV File</label>
                            <input type="file" name="csv_file" class="form-control" accept=".csv" required>
                        </div>
                        <div class="text-center mt-4">
                            <a href="<?= URLROOT ?>/admin/downloadTemplate" class="text-decoration-none">
                                <small>📄 Download CSV Template</small>
                            </a>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="showImportModal = false">Cancel</button>
                        <button type="submit" class="btn btn-primary">Import Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once APPROOT . '/views/layouts/footer.php'; ?>
