<?php require_once APPROOT . '/views/layouts/header.php'; ?>

<div class="container mt-4">
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4 gap-3">
        <h2>Dashboard</h2>
        <a href="<?= URLROOT ?>/transaction" class="btn btn-primary">+ New Transaction</a>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-white bg-success shadow-sm mb-3">
                <div class="card-body">
                    <h5 class="card-title">Total Income</h5>
                    <h3><?= CURRENCY_SYMBOL ?><?= number_format($data['summary']['income'], 2) ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-danger shadow-sm mb-3">
                <div class="card-body">
                    <h5 class="card-title">Total Expenses</h5>
                    <h3><?= CURRENCY_SYMBOL ?><?= number_format($data['summary']['expense'], 2) ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-<?= $data['summary']['balance'] >= 0 ? 'primary' : 'warning' ?> shadow-sm mb-3">
                <div class="card-body">
                    <h5 class="card-title">Net Balance</h5>
                    <h3><?= CURRENCY_SYMBOL ?><?= number_format($data['summary']['balance'], 2) ?></h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Transactions -->
        <div class="col-md-8 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Recent Transactions</h5>
                </div>
        <div class="card-body p-0">
            <?php if(empty($data['transactions'])) : ?>
                <p class="p-4 mb-0 text-center text-muted">No recent transactions found. Start by adding one!</p>
            <?php else : ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Description</th>
                                <th>Category</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
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
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
        </div>

        <!-- Expense Chart -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm h-100 mb-3">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Expenses by Category</h5>
                </div>
                <div class="card-body">
                    <?php if(empty($data['chartData'])) : ?>
                        <p class="text-center text-muted mt-5">No expense data available.</p>
                    <?php else : ?>
                        <canvas id="expenseChart"></canvas>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Weekly Trends</h5>
                </div>
                <div class="card-body">
                    <?php if(empty($data['weeklyTrends'])) : ?>
                        <p class="text-center text-muted mt-5">No weekly data available.</p>
                    <?php else : ?>
                        <canvas id="trendsChart"></canvas>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const chartData = <?= json_encode($data['chartData']) ?>;
        if(chartData && chartData.length > 0) {
            const ctx = document.getElementById('expenseChart').getContext('2d');
            const labels = chartData.map(item => item.name);
            const data = chartData.map(item => item.total);
            const colors = chartData.map(item => item.color_code);

            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: colors,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'bottom' }
                    }
                }
            });
        }

        const trendsData = <?= json_encode($data['weeklyTrends'] ?? []) ?>;
        if(trendsData && trendsData.length > 0) {
            const ctx2 = document.getElementById('trendsChart').getContext('2d');
            const labels2 = trendsData.map(item => item.date);
            const data2 = trendsData.map(item => item.total);

            new Chart(ctx2, {
                type: 'bar',
                data: {
                    labels: labels2,
                    datasets: [{
                        label: 'Daily Expenses',
                        data: data2,
                        backgroundColor: '#dc3545',
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        }
    });
</script>

<?php require_once APPROOT . '/views/layouts/footer.php'; ?>
