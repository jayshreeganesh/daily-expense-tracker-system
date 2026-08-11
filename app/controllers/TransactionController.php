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
        $transactions = $this->transactionModel->getTransactionsByUser($_SESSION['user_id']);
        $categories = $this->categoryModel->getCategoriesByUser($_SESSION['user_id']);
        $data = [
            'transactions' => $transactions,
            'categories' => $categories
        ];
        $this->view('transactions/index', $data);
    }

    public function add() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'user_id' => $_SESSION['user_id'],
                'category_id' => trim($_POST['category_id']),
                'amount' => trim($_POST['amount']),
                'type' => trim($_POST['type']),
                'transaction_date' => trim($_POST['transaction_date']),
                'description' => trim($_POST['description'])
            ];

            if (!empty($data['category_id']) && !empty($data['amount']) && !empty($data['transaction_date'])) {
                if ($this->transactionModel->addTransaction($data)) {
                    flash('transaction_message', 'Transaction Added Successfully');
                    header('Location: ' . URLROOT . '/transactioncontroller');
                    exit;
                } else {
                    die('Something went wrong');
                }
            } else {
                flash('transaction_message', 'Please fill all required fields', 'alert alert-danger');
                header('Location: ' . URLROOT . '/transactioncontroller');
                exit;
            }
        }
    }

    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($this->transactionModel->deleteTransaction($id, $_SESSION['user_id'])) {
                flash('transaction_message', 'Transaction Removed');
                header('Location: ' . URLROOT . '/transactioncontroller');
                exit;
            } else {
                die('Something went wrong');
            }
        } else {
            header('Location: ' . URLROOT . '/transactioncontroller');
            exit;
        }
    }
}
