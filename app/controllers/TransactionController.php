<?php
class TransactionController extends Controller {
    private $transactionModel;
    private $categoryModel;

    public function __construct() {
        if (!isLoggedIn()) {
            header('Location: ' . URLROOT . '/auth/login');
            exit;
        }
        $this->transactionModel = $this->model('Transaction');
        $this->categoryModel = $this->model('Category');
    }

    public function index() {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $month = !empty($_GET['month']) ? $_GET['month'] : null;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $transactions = $this->transactionModel->getTransactionsByUser($_SESSION['user_id'], $limit, $offset, $month);
        $total = $this->transactionModel->getTotalTransactionsByUser($_SESSION['user_id'], $month);
        $totalPages = ceil($total / $limit);

        $categories = $this->categoryModel->getCategoriesByUser($_SESSION['user_id']);
        
        $data = [
            'transactions' => $transactions,
            'categories' => $categories,
            'page' => $page,
            'totalPages' => $totalPages,
            'month' => $month
        ];
        $this->view('transactions/index', $data);
    }

    private function getBaseCurrencyCode() {
        $symbol = defined('CURRENCY_SYMBOL') ? CURRENCY_SYMBOL : '$';
        $map = [
            '$' => 'USD', '€' => 'EUR', '£' => 'GBP',
            '₹' => 'INR', '¥' => 'JPY', 'A$' => 'AUD', 'C$' => 'CAD'
        ];
        return $map[$symbol] ?? 'USD';
    }

    public function add() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $original_amount = (float)trim($_POST['amount']);
            $original_currency = trim($_POST['currency'] ?? $this->getBaseCurrencyCode());
            $base_currency = $this->getBaseCurrencyCode();
            $exchange_rate = 1.0000;
            $amount = $original_amount;

            if ($original_currency !== $base_currency) {
                $ch = curl_init("https://open.er-api.com/v6/latest/" . urlencode($original_currency));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 5);
                $response = curl_exec($ch);
                curl_close($ch);
                if ($response) {
                    $apiData = json_decode($response, true);
                    if (isset($apiData['rates'][$base_currency])) {
                        $exchange_rate = (float)$apiData['rates'][$base_currency];
                        $amount = $original_amount * $exchange_rate;
                    }
                }
            }

            $data = [
                'user_id' => $_SESSION['user_id'],
                'category_id' => trim($_POST['category_id']),
                'amount' => $amount,
                'original_currency' => $original_currency,
                'original_amount' => $original_amount,
                'exchange_rate' => $exchange_rate,
                'type' => trim($_POST['type']),
                'transaction_date' => trim($_POST['transaction_date']),
                'description' => trim($_POST['description'])
            ];

            if (!empty($data['category_id']) && $original_amount > 0 && !empty($data['transaction_date'])) {
                if ($this->transactionModel->addTransaction($data)) {
                    flash('transaction_message', 'Transaction Added Successfully');
                    header('Location: ' . URLROOT . '/transaction');
                    exit;
                } else {
                    die('Something went wrong');
                }
            } else {
                flash('transaction_message', 'Please fill all required fields with valid amounts', 'alert alert-danger');
                header('Location: ' . URLROOT . '/transaction');
                exit;
            }
        }
    }

    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($this->transactionModel->deleteTransaction($id, $_SESSION['user_id'])) {
                flash('transaction_message', 'Transaction Removed');
                header('Location: ' . URLROOT . '/transaction');
                exit;
            } else {
                die('Something went wrong');
            }
        } else {
            header('Location: ' . URLROOT . '/transaction');
            exit;
        }
    }

    public function export() {
        $month = !empty($_GET['month']) ? $_GET['month'] : null;
        // Limit high so it essentially gets all for the month
        $transactions = $this->transactionModel->getTransactionsByUser($_SESSION['user_id'], 10000, 0, $month);
        
        $filename = "transactions_export_" . ($month ? $month : date('Y-m-d')) . ".csv";
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '";');
        
        $output = fopen('php://output', 'w');
        
        // CSV Header
        fputcsv($output, ['Date', 'Category', 'Type', 'Amount', 'Description']);
        
        foreach ($transactions as $tx) {
            fputcsv($output, [
                date('Y-m-d', strtotime($tx->transaction_date)),
                $tx->category_name,
                ucfirst($tx->type),
                $tx->amount,
                $tx->description
            ]);
        }
        
        fclose($output);
        exit;
    }

    public function exportPdf() {
        $month = !empty($_GET['month']) ? $_GET['month'] : null;
        $transactions = $this->transactionModel->getTransactionsByUser($_SESSION['user_id'], 10000, 0, $month);
        
        $data = [
            'transactions' => $transactions,
            'month' => $month
        ];
        
        // Render a dedicated print-friendly view
        $this->view('transactions/pdf', $data);
    }
}
