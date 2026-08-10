<?php
class DashboardController extends Controller {
    public function __construct() {
        // Initialize models here
    }

    public function index() {
        $data = [
            'title' => 'Dashboard'
        ];
        
        $this->view('dashboard/index', $data);
    }
}
