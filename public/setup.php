<?php
if (file_exists(__DIR__ . '/../setup.lock')) {
    die("Setup is already complete. Please delete setup.lock if you wish to run the installer again.");
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $db_host = trim($_POST['db_host']);
    $db_user = trim($_POST['db_user']);
    $db_pass = trim($_POST['db_pass']);
    $db_name = trim($_POST['db_name']);
    
    $admin_name = trim($_POST['admin_name']);
    $admin_email = trim($_POST['admin_email']);
    $admin_pass = trim($_POST['admin_pass']);
    
    $site_url = rtrim(trim($_POST['site_url']), '/');

    try {
        // Step 1: Securely Connect to MySQL
        try {
            // Priority 1: Try connecting directly to the database (Shared Hosting / cPanel behavior)
            $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Unknown database') !== false) {
                // Priority 2: Database doesn't exist, try connecting to server to create it (Localhost / Root behavior)
                $pdo = new PDO("mysql:host=$db_host", $db_user, $db_pass);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db_name`");
                $pdo->exec("USE `$db_name`");
            } else {
                throw $e; // Rethrow if it's an Access Denied or Host Unreachable error
            }
        }

        // Step 2: Import database.sql schema
        $sqlFile = __DIR__ . '/../database.sql';
        if (file_exists($sqlFile)) {
            $sql = file_get_contents($sqlFile);
            $pdo->exec($sql);
        } else {
            throw new Exception("database.sql file missing!");
        }

        // Step 3: Create Super Admin Account
        $hashed_pass = password_hash($admin_pass, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'admin')");
        $stmt->execute([$admin_name, $admin_email, $hashed_pass]);
        $admin_id = $pdo->lastInsertId();

        // Step 4: Write config.php
        $configContent = "<?php
define('DB_HOST', '$db_host');
define('DB_USER', '$db_user');
define('DB_PASS', '$db_pass');
define('DB_NAME', '$db_name');
define('APPROOT', dirname(dirname(__FILE__)));
define('URLROOT', '$site_url');
define('SITENAME', 'Daily Expense Tracker');
";
        file_put_contents(__DIR__ . '/../app/config/config.php', $configContent);

        // Step 5: Seed Demo Data from JSON
        $jsonContent = file_get_contents(__DIR__ . '/../app/config/demo_data.json');
        if ($jsonContent) {
            $demoData = json_decode($jsonContent, true);
            $categories = $demoData['categories'] ?? [];
            $transactions = $demoData['transactions'] ?? [];

            $stmtCat = $pdo->prepare("INSERT INTO categories (user_id, name, type, color_code) VALUES (?, ?, ?, ?)");
            $stmtTxn = $pdo->prepare("INSERT INTO transactions (user_id, category_id, amount, type, transaction_date, description) VALUES (?, ?, ?, ?, ?, ?)");

            $catIds = [];
            foreach ($categories as $cat) {
                $stmtCat->execute([$admin_id, $cat['name'], $cat['type'], $cat['color_code']]);
                $catIds[] = $pdo->lastInsertId();
            }

            foreach ($transactions as $txn) {
                if (isset($catIds[$txn['category_index']])) {
                    $stmtTxn->execute([$admin_id, $catIds[$txn['category_index']], $txn['amount'], $txn['type'], date('Y-m-d'), $txn['description']]);
                }
            }

            $reminders = $demoData['reminders'] ?? [];
            $stmtRem = $pdo->prepare("INSERT INTO reminders (user_id, title, amount, due_date) VALUES (?, ?, ?, ?)");
            foreach ($reminders as $rem) {
                $dueDate = date('Y-m-d', strtotime('+' . $rem['due_date_offset'] . ' days'));
                $stmtRem->execute([$admin_id, $rem['title'], $rem['amount'], $dueDate]);
            }
        }

        // Step 6: Create Lock File
        file_put_contents(__DIR__ . '/../setup.lock', 'LOCKED ' . date('Y-m-d H:i:s'));

        $success = "Setup Complete! The system is locked and secured. Redirecting to login...";
        header("refresh:3;url=index.php");
    } catch (Exception $e) {
        $error = "Setup Failed: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SaaS Platform Installer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white text-center py-4">
                        <h2>⚙️ Application Setup Wizard</h2>
                        <p class="mb-0">Configure Database & Super Admin</p>
                    </div>
                    <div class="card-body p-4">
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>
                        <?php if ($success): ?>
                            <div class="alert alert-success text-center">
                                <h4><?= $success ?></h4>
                            </div>
                        <?php else: ?>
                            <!-- Compatibility Checks -->
                            <div class="alert alert-info">
                                <strong>System Check:</strong><br>
                                PHP Version: <?= phpversion() ?> <?= version_compare(phpversion(), '8.0', '>=') ? '✅' : '❌' ?><br>
                                PDO Extension: <?= extension_loaded('pdo') ? '✅' : '❌' ?><br>
                            </div>

                            <form method="post" action="setup.php">
                                <h5 class="border-bottom pb-2">1. Database Configuration</h5>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label>Database Host</label>
                                        <input type="text" name="db_host" class="form-control" placeholder="e.g. localhost or sql123.epizy.com" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label>Database Name</label>
                                        <input type="text" name="db_name" class="form-control" placeholder="e.g. icei_12345_expense" required>
                                    </div>
                                </div>
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <label>Database User</label>
                                        <input type="text" name="db_user" class="form-control" placeholder="e.g. icei_12345" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label>Database Password</label>
                                        <input type="password" name="db_pass" class="form-control">
                                    </div>
                                </div>

                                <h5 class="border-bottom pb-2">2. Super Admin Details</h5>
                                <div class="mb-3">
                                    <label>Admin Full Name</label>
                                    <input type="text" name="admin_name" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label>Admin Email</label>
                                    <input type="email" name="admin_email" class="form-control" required>
                                </div>
                                <div class="mb-4">
                                    <label>Super Admin Password</label>
                                    <input type="password" name="admin_pass" class="form-control" required minlength="6" placeholder="Choose a strong password">
                                </div>
                            </div>
                            
                            <!-- Step 3: Application Settings -->
                            <div class="mb-4">
                                <h5 class="border-bottom pb-2">3. Application Settings</h5>
                                <div class="mb-3">
                                    <label>Site URL (Where is this app hosted?)</label>
                                    <?php 
                                        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
                                        $defaultUrl = $protocol . $_SERVER['HTTP_HOST'];
                                    ?>
                                    <input type="url" name="site_url" class="form-control" required value="<?php echo htmlspecialchars($defaultUrl); ?>" placeholder="e.g. https://myexpenseapp.com">
                                    <small class="text-muted">Do not include a trailing slash.</small>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">
                                🚀 Install System & Seed Demo Data
                            </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
