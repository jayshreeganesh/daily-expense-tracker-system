<?php
class ProfileController extends Controller {
    private $userModel;

    public function __construct() {
        if (!isLoggedIn()) {
            header('Location: ' . URLROOT . '/auth/login');
            exit;
        }
        $this->userModel = $this->model('User');
    }

    public function index() {
        $user = $this->userModel->getUserById($_SESSION['user_id']);
        
        $data = [
            'user' => $user,
            'name_err' => '',
            'email_err' => '',
            'password_err' => '',
            'confirm_password_err' => ''
        ];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            
            // Check if action is profile update or password update
            if (isset($_POST['update_profile'])) {
                $name = trim($_POST['name']);
                $email = trim($_POST['email']);
                
                if (empty($name)) $data['name_err'] = 'Please enter name';
                if (empty($email)) $data['email_err'] = 'Please enter email';
                
                // Check if email changed and exists
                if ($email != $user->email && $this->userModel->findUserByEmail($email)) {
                    $data['email_err'] = 'Email is already taken';
                }

                if (empty($data['name_err']) && empty($data['email_err'])) {
                    if ($this->userModel->updateProfile($_SESSION['user_id'], $name, $email)) {
                        $_SESSION['user_name'] = $name;
                        $_SESSION['user_email'] = $email;
                        flash('profile_message', 'Profile Updated Successfully');
                        header('Location: ' . URLROOT . '/profile');
                        exit;
                    }
                }
            } elseif (isset($_POST['update_password'])) {
                $password = trim($_POST['password']);
                $confirm_password = trim($_POST['confirm_password']);

                if (empty($password) || strlen($password) < 6) {
                    $data['password_err'] = 'Password must be at least 6 characters';
                }
                if ($password != $confirm_password) {
                    $data['confirm_password_err'] = 'Passwords do not match';
                }

                if (empty($data['password_err']) && empty($data['confirm_password_err'])) {
                    if ($this->userModel->updatePassword($_SESSION['user_id'], $password)) {
                        flash('profile_message', 'Password Updated Successfully');
                        header('Location: ' . URLROOT . '/profile');
                        exit;
                    }
                }
            } elseif (isset($_POST['update_budget'])) {
                $budget = trim($_POST['monthly_budget']);
                if (is_numeric($budget) && $budget >= 0) {
                    if ($this->userModel->updateBudget($_SESSION['user_id'], $budget)) {
                        flash('profile_message', 'Monthly Budget Updated Successfully');
                        header('Location: ' . URLROOT . '/profile');
                        exit;
                    }
                } else {
                    flash('profile_message', 'Invalid budget amount', 'alert alert-danger');
                }
            } elseif (isset($_POST['delete_account'])) {
                if ($this->userModel->deleteAccount($_SESSION['user_id'])) {
                    header('Location: ' . URLROOT . '/auth/logout');
                    exit;
                }
            }
        }
        
        $this->view('profile/index', $data);
    }
}
