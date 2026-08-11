<?php
class DashboardController extends Controller {
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
        $user_id = $_SESSION['user_id'];
        $transactions = $this->transactionModel->getTransactionsByUser($user_id, 5); // get last 5
        $summary = $this->transactionModel->getSummaryByUser($user_id);

        $data = [
            'title' => 'Dashboard',
            'transactions' => $transactions,
            'summary' => $summary
        ];
        
        $this->view('dashboard/index', $data);
    }
}
