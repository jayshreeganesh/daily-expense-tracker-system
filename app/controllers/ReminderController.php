<?php
class ReminderController extends Controller {
    private $reminderModel;

    public function __construct() {
        if (!isLoggedIn()) {
            header('Location: ' . URLROOT . '/auth/login');
            exit;
        }
        $this->reminderModel = $this->model('Reminder');
    }

    public function index() {
        $reminders = $this->reminderModel->getRemindersByUser($_SESSION['user_id']);
        
        $data = [
            'title' => 'Reminders',
            'reminders' => $reminders
        ];
        
        $this->view('reminders/index', $data);
    }
    
    public function add() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'user_id' => $_SESSION['user_id'],
                'title' => trim($_POST['title']),
                'amount' => trim($_POST['amount']),
                'due_date' => trim($_POST['due_date'])
            ];
            
            if(!empty($data['title']) && !empty($data['due_date'])) {
                $this->reminderModel->addReminder($data);
                // We could add an audit log here if we want:
                log_audit($_SESSION['user_id'], 'Added Reminder', 'Reminder', null, 'Title: ' . $data['title']);
            }
        }
        header('Location: ' . URLROOT . '/reminder');
        exit;
    }
    
    public function complete($id) {
        $this->reminderModel->completeReminder($id, $_SESSION['user_id']);
        log_audit($_SESSION['user_id'], 'Completed Reminder', 'Reminder', $id);
        header('Location: ' . URLROOT . '/reminder');
        exit;
    }

    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->reminderModel->deleteReminder($id, $_SESSION['user_id']);
            log_audit($_SESSION['user_id'], 'Deleted Reminder', 'Reminder', $id);
        }
        header('Location: ' . URLROOT . '/reminder');
        exit;
    }
}
