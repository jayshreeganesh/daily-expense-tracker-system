<?php
class Category {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function getCategoriesByUser($user_id) {
        $this->db->query('SELECT * FROM categories WHERE user_id = :user_id ORDER BY name ASC');
        $this->db->bind(':user_id', $user_id);
        return $this->db->resultSet();
    }

    public function addCategory($data) {
        $this->db->query('INSERT INTO categories (user_id, name, type, color_code) VALUES (:user_id, :name, :type, :color_code)');
        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':type', $data['type']);
        $this->db->bind(':color_code', $data['color_code']);

        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }
}
