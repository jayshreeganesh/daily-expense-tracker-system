<?php
class AdminController extends Controller {
    private $adminModel;

    public function __construct() {
        if (!isLoggedIn() || !in_array($_SESSION['user_role'], ['admin', 'recruiter'])) {
            header('Location: ' . URLROOT . '/dashboard');
            exit;
        }
        $this->adminModel = $this->model('Admin');
    }

    public function index() {
        $stats = $this->adminModel->getSystemStats();
        $users = $this->adminModel->getAllUsers();
        $auditLogs = $this->adminModel->getAuditLogs();
        
        $data = [
            'title' => 'Admin Dashboard',
            'stats' => $stats,
            'users' => $users,
            'auditLogs' => $auditLogs
        ];
        
        $this->view('admin/index', $data);
    }
    
    public function backup() {
        $file = $this->adminModel->createBackup();
        if ($file) {
            header('Content-Type: application/sql');
            header('Content-Disposition: attachment; filename="backup.sql"');
            readfile($file);
            unlink($file);
            exit;
        } else {
            header('Location: ' . URLROOT . '/admin');
            exit;
        }
    }

    public function downloadTemplate() {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="transactions_template.csv"');
        $output = fopen('php://output', 'w');
        fputcsv($output, ['User Email', 'Category Name', 'Type (income/expense)', 'Amount', 'Date (YYYY-MM-DD)', 'Description']);
        fputcsv($output, ['user@example.com', 'Salary', 'income', '5000', '2023-10-01', 'October Salary']);
        fclose($output);
        exit;
    }

    public function importTransactions() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['csv_file'])) {
            $file = $_FILES['csv_file']['tmp_name'];
            if (($handle = fopen($file, "r")) !== FALSE) {
                fgetcsv($handle); // skip header
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    if (count($data) >= 6) {
                        $this->adminModel->importTransaction($data[0], $data[1], $data[2], $data[3], $data[4], $data[5]);
                    }
                }
                fclose($handle);
            }
        }
        header('Location: ' . URLROOT . '/admin');
        exit;
    }

    public function updateRole() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['user_id']) && isset($_POST['role'])) {
            $user_id = $_POST['user_id'];
            $role = $_POST['role'];
            
            // Prevent admin from accidentally demoting themselves
            if ($user_id != $_SESSION['user_id']) {
                $this->adminModel->updateUserRole($user_id, $role);
            }
        }
        header('Location: ' . URLROOT . '/admin');
        exit;
    }

    public function createUser() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = trim($_POST['name']);
            $email = trim($_POST['email']);
            $password = trim($_POST['password']);
            $role = trim($_POST['role']);
            
            if(!empty($name) && !empty($email) && !empty($password)) {
                $this->adminModel->createUser($name, $email, $password, $role);
            }
        }
        header('Location: ' . URLROOT . '/admin');
        exit;
    }

    public function seedData() {
        if ($_SESSION['user_role'] === 'admin') {
            $this->adminModel->seedDemoData();
        }
        header('Location: ' . URLROOT . '/admin');
        exit;
    }

    public function createZip() {
        if ($_SESSION['user_role'] !== 'admin') {
            header('Location: ' . URLROOT . '/admin');
            exit;
        }

        $zipFile = APPROOT . '/../project_export.zip';
        $zip = new ZipArchive();
        if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            $dir = realpath(APPROOT . '/../');
            // Use SELF_FIRST to ensure directory entries are created
            $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir), RecursiveIteratorIterator::SELF_FIRST);
            
            // Load exclusions once outside the loop for performance
            $jsonExclusions = json_decode(file_get_contents(APPROOT . '/config/zip_exclusions.json'), true);
            $excludeDirs = $jsonExclusions['directories'] ?? [];
            $excludeFiles = $jsonExclusions['files'] ?? [];

            foreach ($files as $name => $file) {
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($dir) + 1);
                
                // CRITICAL: Normalize slashes to forward slashes for Linux compatibility
                $normalizedPath = str_replace('\\', '/', $relativePath);
                
                // Skip empty root path
                if (empty($normalizedPath)) continue;

                $skip = false;
                
                // Append trailing slash for directory matching
                $checkPath = '/' . $normalizedPath . ($file->isDir() ? '/' : '');
                
                foreach ($excludeDirs as $exclude) {
                    if (strpos($checkPath, $exclude) !== false) {
                        $skip = true;
                        break;
                    }
                }
                
                if (!$file->isDir() && in_array(basename($normalizedPath), $excludeFiles)) {
                    $skip = true;
                }
                
                if (!$skip) {
                    if ($file->isDir()) {
                        $zip->addEmptyDir($normalizedPath);
                    } else {
                        $zip->addFile($filePath, $normalizedPath);
                    }
                }
            }
            $zip->close();
            
            // Add a unique timestamp so browsers don't block multiple downloads
            $uniqueFilename = 'daily_expense_tracker_' . date('Y_m_d_H_i_s') . '.zip';
            
            // Force browser not to cache the download request
            header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
            header("Cache-Control: post-check=0, pre-check=0", false);
            header("Pragma: no-cache");
            
            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename="' . $uniqueFilename . '"');
            header('Content-Length: ' . filesize($zipFile));
            readfile($zipFile);
            unlink($zipFile);
            exit;
        }
        header('Location: ' . URLROOT . '/admin');
        exit;
    }
}
