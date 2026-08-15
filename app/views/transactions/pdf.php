<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Export PDF - <?= SITENAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { font-size: 12pt; background: #fff; }
            .table { border-collapse: collapse !important; }
            .table td, .table th { background-color: #fff !important; }
        }
        body { background-color: #f8f9fa; padding: 20px; }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            background-color: #fff;
            font-size: 16px;
            line-height: 24px;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="invoice-box">
        <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
            <div>
                <h2 class="mb-0 text-dark"><?= SITENAME ?></h2>
                <div class="text-muted">Transaction Report</div>
            </div>
            <div class="text-end">
                <div class="fw-bold">Generated: <?= date('F d, Y') ?></div>
                <?php if(!empty($data['month'])): ?>
                <div class="badge bg-secondary">Period: <?= date('F Y', strtotime($data['month'] . '-01')) ?></div>
                <?php else: ?>
                <div class="badge bg-secondary">Period: All Time</div>
                <?php endif; ?>
            </div>
        </div>

        <table class="table table-bordered table-striped mt-4">
            <thead class="table-dark">
                <tr>
                    <th>Date</th>
                    <th>Category</th>
                    <th>Description</th>
                    <th class="text-end">Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $totalIncome = 0;
                $totalExpense = 0;
                if(empty($data['transactions'])): 
                ?>
                    <tr><td colspan="4" class="text-center">No transactions found for this period.</td></tr>
                <?php 
                else:
                    foreach($data['transactions'] as $tx): 
                        if ($tx->type == 'income') {
                            $totalIncome += $tx->amount;
                        } else {
                            $totalExpense += $tx->amount;
                        }
                ?>
                    <tr>
                        <td><?= date('M d, Y', strtotime($tx->transaction_date)) ?></td>
                        <td><?= htmlspecialchars($tx->category_name) ?></td>
                        <td><?= htmlspecialchars($tx->description) ?></td>
                        <td class="text-end fw-bold <?= $tx->type == 'income' ? 'text-success' : 'text-danger' ?>">
                            <?= $tx->type == 'income' ? '+' : '-' ?><?= CURRENCY_SYMBOL ?><?= number_format($tx->amount, 2) ?>
                        </td>
                    </tr>
                <?php 
                    endforeach; 
                endif;
                ?>
            </tbody>
            <tfoot class="table-light">
                <tr>
                    <th colspan="3" class="text-end">Total Income:</th>
                    <th class="text-end text-success">+<?= CURRENCY_SYMBOL ?><?= number_format($totalIncome, 2) ?></th>
                </tr>
                <tr>
                    <th colspan="3" class="text-end">Total Expense:</th>
                    <th class="text-end text-danger">-<?= CURRENCY_SYMBOL ?><?= number_format($totalExpense, 2) ?></th>
                </tr>
                <tr>
                    <th colspan="3" class="text-end text-dark">Net Balance:</th>
                    <th class="text-end <?= ($totalIncome - $totalExpense) >= 0 ? 'text-success' : 'text-danger' ?>">
                        <?= ($totalIncome - $totalExpense) >= 0 ? '+' : '' ?><?= CURRENCY_SYMBOL ?><?= number_format($totalIncome - $totalExpense, 2) ?>
                    </th>
                </tr>
            </tfoot>
        </table>

        <div class="text-center mt-5 no-print">
            <button onclick="window.print()" class="btn btn-primary btn-lg px-5 shadow">🖨️ Print / Save as PDF</button>
            <br>
            <a href="<?= URLROOT ?>/transaction" class="btn btn-link text-muted mt-2">← Back to Dashboard</a>
        </div>
        
        <script>
            // Automatically prompt the print dialog on load
            window.onload = function() {
                window.print();
            };
        </script>
    </div>
</body>
</html>
