<?php
class DashboardController extends Controller {
    private $transactionModel;
    private $categoryModel;
    public function __construct() {
        if (!isLoggedIn()) {
            header('Location: ' . URLROOT . '/auth');
            exit;
        }
        $this->transactionModel = $this->model('Transaction');
        $this->categoryModel = $this->model('Category');
    }

    public function index() {
        $user_id = $_SESSION['user_id'];
        
        $userModel = $this->model('User');
        $user = $userModel->getUserById($user_id);
        $monthly_budget = $user->monthly_budget;

        $transactions = $this->transactionModel->getTransactionsByUser($user_id, 5); // get last 5
        $summary = $this->transactionModel->getSummaryByUser($user_id);
        $currentMonthSummary = $this->transactionModel->getSummaryByUser($user_id, date('Y-m'));
        
        $expensesByCategory = $this->transactionModel->getExpensesByCategory($user_id);
        $weeklyTrends = $this->transactionModel->getWeeklyTrends($user_id);
        $sixMonthTrends = $this->transactionModel->getSixMonthTrends($user_id);

        $data = [
            'title' => 'Dashboard',
            'transactions' => $transactions,
            'summary' => $summary,
            'currentMonthSummary' => $currentMonthSummary,
            'monthly_budget' => $monthly_budget,
            'chartData' => $expensesByCategory,
            'weeklyTrends' => $weeklyTrends,
            'sixMonthTrends' => $sixMonthTrends
        ];
        
        $this->view('dashboard/index', $data);
    }
}
