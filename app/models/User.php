<?php
class User {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    // Register User
    public function register($data) {
        $this->db->query('INSERT INTO users (name, email, password) VALUES(:name, :email, :password)');
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':password', $data['password']);

        if ($this->db->execute()) {
            $user_id = $this->db->lastInsertId();
            
            // Insert Default Categories
            if (defined('DEFAULT_EXPENSE_CATEGORIES')) {
                $expense_cats = array_filter(array_map('trim', explode(',', DEFAULT_EXPENSE_CATEGORIES)));
                foreach($expense_cats as $cat) {
                    $this->db->query('INSERT INTO categories (user_id, name, type, color_code) VALUES (:uid, :name, :type, :color)');
                    $this->db->bind(':uid', $user_id);
                    $this->db->bind(':name', $cat);
                    $this->db->bind(':type', 'expense');
                    $this->db->bind(':color', '#' . substr(md5(rand()), 0, 6)); // Random color
                    $this->db->execute();
                }
            }

            if (defined('DEFAULT_INCOME_CATEGORIES')) {
                $income_cats = array_filter(array_map('trim', explode(',', DEFAULT_INCOME_CATEGORIES)));
                foreach($income_cats as $cat) {
                    $this->db->query('INSERT INTO categories (user_id, name, type, color_code) VALUES (:uid, :name, :type, :color)');
                    $this->db->bind(':uid', $user_id);
                    $this->db->bind(':name', $cat);
                    $this->db->bind(':type', 'income');
                    $this->db->bind(':color', '#' . substr(md5(rand()), 0, 6));
                    $this->db->execute();
                }
            }
            
            return true;
        } else {
            return false;
        }
    }

    // Login User
    public function login($email, $password) {
        $this->db->query('SELECT * FROM users WHERE email = :email');
        $this->db->bind(':email', $email);

        $row = $this->db->single();

        if ($row) {
            $hashed_password = $row->password;
            if (password_verify($password, $hashed_password)) {
                return $row;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    // Find user by email
    public function findUserByEmail($email) {
        $this->db->query('SELECT * FROM users WHERE email = :email');
        $this->db->bind(':email', $email);
        $row = $this->db->single();
        if ($this->db->rowCount() > 0) {
            return $row; // Return user object instead of true
        } else {
            return false;
        }
    }

    public function getUserById($id) {
        $this->db->query('SELECT * FROM users WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function updateProfile($id, $name, $email) {
        $this->db->query('UPDATE users SET name = :name, email = :email WHERE id = :id');
        $this->db->bind(':name', $name);
        $this->db->bind(':email', $email);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function updatePassword($id, $password) {
        $this->db->query('UPDATE users SET password = :password WHERE id = :id');
        $this->db->bind(':password', password_hash($password, PASSWORD_DEFAULT));
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function setResetToken($email, $token) {
        // We will add reset_token and reset_expires columns dynamically if they don't exist
        try {
            $this->db->query('ALTER TABLE users ADD COLUMN reset_token VARCHAR(255) NULL, ADD COLUMN reset_expires DATETIME NULL');
            $this->db->execute();
        } catch(PDOException $e) {} // Ignore if columns already exist

        $expires = date('Y-m-d H:i:s', time() + 3600); // 1 hour from now
        $this->db->query('UPDATE users SET reset_token = :token, reset_expires = :expires WHERE email = :email');
        $this->db->bind(':token', password_hash($token, PASSWORD_DEFAULT));
        $this->db->bind(':expires', $expires);
        $this->db->bind(':email', $email);
        return $this->db->execute();
    }

    public function getUserByToken($email, $token) {
        $this->db->query('SELECT * FROM users WHERE email = :email AND reset_expires > NOW()');
        $this->db->bind(':email', $email);
        $user = $this->db->single();
        
        if($user && password_verify($token, $user->reset_token)) {
            return $user;
        }
        return false;
    }

    public function deleteAccount($id) {
        $this->db->query('DELETE FROM users WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}
