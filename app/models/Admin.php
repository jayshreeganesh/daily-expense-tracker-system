<?php
class Admin {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function getSystemStats() {
        $this->db->query('SELECT COUNT(*) as total_users FROM users');
        $users = $this->db->single()->total_users;

        $this->db->query('SELECT COUNT(*) as total_transactions FROM transactions');
        $transactions = $this->db->single()->total_transactions;

        $this->db->query('SELECT SUM(amount) as total_money FROM transactions WHERE type="income"');
        $total_money = $this->db->single()->total_money;

        return [
            'users' => $users ?? 0,
            'transactions' => $transactions ?? 0,
            'total_money' => $total_money ?? 0
        ];
    }

    public function getAllUsers() {
        $this->db->query('SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC');
        return $this->db->resultSet();
    }

    public function getAuditLogs($limit = 50) {
        $this->db->query('SELECT a.*, u.name as user_name FROM audit_logs a JOIN users u ON a.user_id = u.id ORDER BY a.created_at DESC LIMIT :limit');
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        return $this->db->resultSet();
    }
    
    public function createBackup() {
        $filename = APPROOT . '/../backup_' . date('Y_m_d_H_i_s') . '.sql';
        
        // Simple fallback if mysqldump is not available
        $output = "-- Database Backup\n";
        $output .= "-- Generated: " . date('Y-m-d H:i:s') . "\n\n";
        
        $tables = ['users', 'categories', 'transactions'];
        foreach ($tables as $table) {
            $this->db->query("SELECT * FROM $table");
            $rows = $this->db->resultSet();
            
            foreach ($rows as $row) {
                $values = array_map(function($val) {
                    return "'" . addslashes($val) . "'";
                }, (array)$row);
                $output .= "INSERT INTO $table (" . implode(', ', array_keys((array)$row)) . ") VALUES (" . implode(', ', $values) . ");\n";
            }
            $output .= "\n";
        }
        
        file_put_contents($filename, $output);
        return $filename;
    }

    public function importTransaction($email, $cat_name, $type, $amount, $date, $desc) {
        $this->db->query('SELECT id FROM users WHERE email = :email');
        $this->db->bind(':email', $email);
        $user = $this->db->single();
        if (!$user) return false;

        $this->db->query('SELECT id FROM categories WHERE user_id = :user_id AND name = :name');
        $this->db->bind(':user_id', $user->id);
        $this->db->bind(':name', $cat_name);
        $cat = $this->db->single();
        
        if ($cat) {
            $category_id = $cat->id;
        } else {
            $this->db->query('INSERT INTO categories (user_id, name, type, color_code) VALUES (:user_id, :name, :type, :color)');
            $this->db->bind(':user_id', $user->id);
            $this->db->bind(':name', $cat_name);
            $this->db->bind(':type', $type);
            $this->db->bind(':color', '#6c757d');
            $this->db->execute();
            $category_id = $this->db->lastInsertId();
        }

        $this->db->query('INSERT INTO transactions (user_id, category_id, amount, type, transaction_date, description) VALUES (:user_id, :category_id, :amount, :type, :transaction_date, :description)');
        $this->db->bind(':user_id', $user->id);
        $this->db->bind(':category_id', $category_id);
        $this->db->bind(':amount', $amount);
        $this->db->bind(':type', $type);
        $this->db->bind(':transaction_date', $date);
        $this->db->bind(':description', $desc);
        $this->db->execute();
        
        log_audit($_SESSION['user_id'], 'Bulk Imported Transaction', 'Transaction', $this->db->lastInsertId(), "For user: $email");
        return true;
    }

    public function updateUserRole($user_id, $role) {
        $this->db->query('UPDATE users SET role = :role WHERE id = :id');
        $this->db->bind(':role', $role);
        $this->db->bind(':id', $user_id);
        if($this->db->execute()) {
            log_audit($_SESSION['user_id'], 'Updated User Role', 'User', $user_id, "New Role: $role");
            return true;
        }
        return false;
    }

    public function createUser($name, $email, $password, $role) {
        $this->db->query('INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, :role)');
        $this->db->bind(':name', $name);
        $this->db->bind(':email', $email);
        $this->db->bind(':password', password_hash($password, PASSWORD_DEFAULT));
        $this->db->bind(':role', $role);
        if($this->db->execute()) {
            log_audit($_SESSION['user_id'], 'Created User', 'User', $this->db->lastInsertId(), "Email: $email, Role: $role");
            return true;
        }
        return false;
    }

    public function seedDemoData() {
        $user_id = $_SESSION['user_id'];
        
        // 1. Reset existing data for the current user
        $this->db->query('DELETE FROM categories WHERE user_id = :uid');
        $this->db->bind(':uid', $user_id);
        $this->db->execute(); // This will cascade delete transactions
        
        $this->db->query('DELETE FROM reminders WHERE user_id = :uid');
        $this->db->bind(':uid', $user_id);
        $this->db->execute();

        // 2. Ensure the Portfolio 'Demo Admin' user exists for recruiters
        $this->db->query('SELECT id FROM users WHERE email = :email');
        $this->db->bind(':email', 'admin@example.com');
        $demoAdmin = $this->db->single();
        
        if (!$demoAdmin) {
            $this->db->query('INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, :role)');
            $this->db->bind(':name', 'Demo Admin');
            $this->db->bind(':email', 'admin@example.com');
            $this->db->bind(':password', password_hash('password123', PASSWORD_DEFAULT));
            $this->db->bind(':role', 'admin');
            $this->db->execute();
            $demoAdminId = $this->db->lastInsertId();
        } else {
            $demoAdminId = $demoAdmin->id;
            // Clear demo admin data so it gets fresh seed data
            $this->db->query('DELETE FROM categories WHERE user_id = :uid');
            $this->db->bind(':uid', $demoAdminId);
            $this->db->execute();
            $this->db->query('DELETE FROM reminders WHERE user_id = :uid');
            $this->db->bind(':uid', $demoAdminId);
            $this->db->execute();
        }

        // We will seed data for BOTH the current user AND the demo admin
        $targetUsers = array_unique([$user_id, $demoAdminId]);

        $jsonData = json_decode(file_get_contents(APPROOT . '/config/demo_data.json'), true);
        $categories = $jsonData['categories'] ?? [];
        $transactions = $jsonData['transactions'] ?? [];
        $reminders = $jsonData['reminders'] ?? [];
        
        foreach($targetUsers as $targetUserId) {
            $catIds = [];
            foreach($categories as $cat) {
                $this->db->query('INSERT INTO categories (user_id, name, type, color_code) VALUES (:uid, :name, :type, :color)');
                $this->db->bind(':uid', $targetUserId);
                $this->db->bind(':name', $cat['name']);
                $this->db->bind(':type', $cat['type']);
                $this->db->bind(':color', $cat['color_code']);
                $this->db->execute();
                $catIds[] = $this->db->lastInsertId();
            }

            foreach($transactions as $txn) {
                if(isset($catIds[$txn['category_index']])) {
                    $this->db->query('INSERT INTO transactions (user_id, category_id, amount, type, transaction_date, description) VALUES (:uid, :cid, :amt, :type, :dt, :desc)');
                    $this->db->bind(':uid', $targetUserId);
                    $this->db->bind(':cid', $catIds[$txn['category_index']]);
                    $this->db->bind(':amt', $txn['amount']);
                    $this->db->bind(':type', $txn['type']);
                    $this->db->bind(':dt', date('Y-m-d'));
                    $this->db->bind(':desc', $txn['description']);
                    $this->db->execute();
                }
            }
            
            foreach($reminders as $rem) {
                $this->db->query('INSERT INTO reminders (user_id, title, amount, due_date) VALUES (:uid, :title, :amount, :due_date)');
                $this->db->bind(':uid', $targetUserId);
                $this->db->bind(':title', $rem['title']);
                $this->db->bind(':amount', $rem['amount']);
                $this->db->bind(':due_date', date('Y-m-d', strtotime('+' . $rem['due_date_offset'] . ' days')));
                $this->db->execute();
            }
        }
        
        log_audit($_SESSION['user_id'], 'Reset & Seeded Demo Data', 'System', null, "Cleared existing data and applied JSON seed");
        return true;
    }
}
