<?php
class CategoryController extends Controller {
    private $categoryModel;

    public function __construct() {
        if (!isLoggedIn()) {
            header('Location: ' . URLROOT . '/auth/login');
            exit;
        }
        $this->categoryModel = $this->model('Category');
    }

    public function index() {
        $categories = $this->categoryModel->getCategoriesByUser($_SESSION['user_id']);
        $data = [
            'categories' => $categories
        ];
        $this->view('categories/index', $data);
    }

    public function add() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'user_id' => $_SESSION['user_id'],
                'name' => trim($_POST['name']),
                'type' => trim($_POST['type']),
                'color_code' => trim($_POST['color_code']),
            ];

            if (!empty($data['name']) && !empty($data['type'])) {
                if ($this->categoryModel->addCategory($data)) {
                    flash('category_message', 'Category Added');
                    header('Location: ' . URLROOT . '/categorycontroller');
                    exit;
                } else {
                    die('Something went wrong');
                }
            } else {
                flash('category_message', 'Please fill in all fields', 'alert alert-danger');
                header('Location: ' . URLROOT . '/categorycontroller');
                exit;
            }
        } else {
            header('Location: ' . URLROOT . '/categorycontroller');
            exit;
        }
    }
}
