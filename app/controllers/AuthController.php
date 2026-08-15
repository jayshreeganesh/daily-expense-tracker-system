<?php
class AuthController extends Controller {
    private $userModel;

    public function __construct() {
        $this->userModel = $this->model('User');
    }

    public function index() {
        if(isLoggedIn()){
            header('Location: ' . URLROOT . '/dashboard');
            exit;
        } else {
            $this->view('auth/index');
        }
    }

    public function adminLogin() {
        if (isLoggedIn()) {
            header('Location: ' . URLROOT . '/dashboard');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'email' => trim($_POST['email']),
                'password' => trim($_POST['password']),
                'email_err' => '',
                'password_err' => ''
            ];

            if (empty($data['email'])) {
                $data['email_err'] = 'Please enter email';
            }
            if (empty($data['password'])) {
                $data['password_err'] = 'Please enter password';
            }

            if (!$this->userModel->findUserByEmail($data['email'])) {
                $data['email_err'] = 'No user found';
            }

            if (empty($data['email_err']) && empty($data['password_err'])) {
                $loggedInUser = $this->userModel->login($data['email'], $data['password']);

                if ($loggedInUser) {
                    if (in_array($loggedInUser->role, ['admin', 'recruiter'])) {
                        $this->createUserSession($loggedInUser);
                    } else {
                        $data['password_err'] = 'Access Denied. You are not an Admin.';
                        $this->view('auth/admin_login', $data);
                    }
                } else {
                    $data['password_err'] = 'Password incorrect';
                    $this->view('auth/admin_login', $data);
                }
            } else {
                $this->view('auth/admin_login', $data);
            }
        } else {
            $data = [
                'email' => '',
                'password' => '',
                'email_err' => '',
                'password_err' => ''
            ];
            $this->view('auth/admin_login', $data);
        }
    }

    public function register() {
        if (isLoggedIn()) {
            header('Location: ' . URLROOT . '/dashboard');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'name' => trim($_POST['name']),
                'email' => trim($_POST['email']),
                'password' => trim($_POST['password']),
                'confirm_password' => trim($_POST['confirm_password']),
                'name_err' => '',
                'email_err' => '',
                'password_err' => '',
                'confirm_password_err' => ''
            ];

            if (empty($data['name'])) {
                $data['name_err'] = 'Please enter name';
            }
            if (empty($data['email'])) {
                $data['email_err'] = 'Please enter email';
            } else {
                if ($this->userModel->findUserByEmail($data['email'])) {
                    $data['email_err'] = 'Email is already taken';
                }
            }
            if (empty($data['password'])) {
                $data['password_err'] = 'Please enter password';
            } elseif (strlen($data['password']) < 6) {
                $data['password_err'] = 'Password must be at least 6 characters';
            }
            if (empty($data['confirm_password'])) {
                $data['confirm_password_err'] = 'Please confirm password';
            } else {
                if ($data['password'] != $data['confirm_password']) {
                    $data['confirm_password_err'] = 'Passwords do not match';
                }
            }

            if (empty($data['name_err']) && empty($data['email_err']) && empty($data['password_err']) && empty($data['confirm_password_err'])) {
                $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
                if ($this->userModel->register($data)) {
                    flash('register_success', 'You are registered and can log in');
                    header('Location: ' . URLROOT . '/auth/login');
                    exit;
                } else {
                    die('Something went wrong');
                }
            } else {
                $this->view('auth/register', $data);
            }
        } else {
            $data = [
                'name' => '',
                'email' => '',
                'password' => '',
                'confirm_password' => '',
                'name_err' => '',
                'email_err' => '',
                'password_err' => '',
                'confirm_password_err' => ''
            ];
            $this->view('auth/register', $data);
        }
    }

    public function login() {
        if (isLoggedIn()) {
            header('Location: ' . URLROOT . '/dashboard');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'email' => trim($_POST['email']),
                'password' => trim($_POST['password']),
                'email_err' => '',
                'password_err' => ''
            ];

            if (empty($data['email'])) {
                $data['email_err'] = 'Please enter email';
            }
            if (empty($data['password'])) {
                $data['password_err'] = 'Please enter password';
            }

            if (!$this->userModel->findUserByEmail($data['email'])) {
                $data['email_err'] = 'No user found';
            }

            if (empty($data['email_err']) && empty($data['password_err'])) {
                $loggedInUser = $this->userModel->login($data['email'], $data['password']);

                if ($loggedInUser) {
                    $this->createUserSession($loggedInUser);
                } else {
                    $data['password_err'] = 'Password incorrect';
                    $this->view('auth/login', $data);
                }
            } else {
                $this->view('auth/login', $data);
            }
        } else {
            $data = [
                'email' => '',
                'password' => '',
                'email_err' => '',
                'password_err' => ''
            ];
            $this->view('auth/login', $data);
        }
    }

    public function createUserSession($user) {
        $_SESSION['user_id'] = $user->id;
        $_SESSION['user_email'] = $user->email;
        $_SESSION['user_name'] = $user->name;
        $_SESSION['user_role'] = $user->role;
        header('Location: ' . URLROOT . '/dashboard');
        exit;
    }

    public function logout() {
        unset($_SESSION['user_id']);
        unset($_SESSION['user_email']);
        unset($_SESSION['user_name']);
        unset($_SESSION['user_role']);
        session_destroy();
        header('Location: ' . URLROOT . '/auth');
        exit;
    }

    public function forgotPassword() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            $email = trim($_POST['email']);
            
            if (empty($email)) {
                $data = ['email_err' => 'Please enter email', 'email' => ''];
                $this->view('auth/forgot_password', $data);
                return;
            }

            if ($this->userModel->findUserByEmail($email)) {
                $token = bin2hex(random_bytes(32));
                $this->userModel->setResetToken($email, $token);
                // In a real app, send email here. For demo, we just show the link.
                $resetLink = URLROOT . '/auth/resetPassword?email=' . urlencode($email) . '&token=' . $token;
                flash('reset_message', 'Reset link generated: <a href="'.$resetLink.'">Click here to reset password</a>', 'alert alert-info');
            } else {
                flash('reset_message', 'No account found with that email.', 'alert alert-danger');
            }
            header('Location: ' . URLROOT . '/auth/forgotPassword');
            exit;
        } else {
            $data = ['email' => '', 'email_err' => ''];
            $this->view('auth/forgot_password', $data);
        }
    }

    public function resetPassword() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            $email = trim($_POST['email']);
            $token = trim($_POST['token']);
            $password = trim($_POST['password']);
            $confirm = trim($_POST['confirm_password']);

            $data = [
                'email' => $email,
                'token' => $token,
                'password_err' => '',
                'confirm_err' => ''
            ];

            if (empty($password) || strlen($password) < 6) {
                $data['password_err'] = 'Password must be at least 6 characters';
            }
            if ($password != $confirm) {
                $data['confirm_err'] = 'Passwords do not match';
            }

            if (empty($data['password_err']) && empty($data['confirm_err'])) {
                $user = $this->userModel->getUserByToken($email, $token);
                if ($user) {
                    $this->userModel->updatePassword($user->id, $password);
                    flash('register_success', 'Password updated! You can now log in.');
                    header('Location: ' . URLROOT . '/auth/login');
                    exit;
                } else {
                    die('Invalid or expired token.');
                }
            } else {
                $this->view('auth/reset_password', $data);
            }
        } else {
            if(!isset($_GET['email']) || !isset($_GET['token'])) {
                die('Invalid request');
            }
            $data = [
                'email' => $_GET['email'],
                'token' => $_GET['token'],
                'password_err' => '',
                'confirm_err' => ''
            ];
            $this->view('auth/reset_password', $data);
        }
    }
}
