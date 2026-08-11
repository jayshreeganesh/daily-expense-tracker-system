<?php
class DashboardController extends Controller {
    public function __construct() {
        if (!isLoggedIn()) {
            header('Location: ' . URLROOT . '/auth/login');
        }
        // Initialize models here
    }

    public function index() {
        $data = [
            'title' => 'Dashboard'
        ];
        
        $this->view('dashboard/index', $data);
    }
}
