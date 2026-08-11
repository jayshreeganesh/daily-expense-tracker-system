<?php
class Transaction {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function getTransactionsByUser($user_id, $limit = null) {
        $sql = 'SELECT t.*, c.name as category_name, c.color_code 
                FROM transactions t 
                LEFT JOIN categories c ON t.category_id = c.id 
                WHERE t.user_id = :user_id 
                ORDER BY t.transaction_date DESC, t.created_at DESC';
        if ($limit) {
            $sql .= ' LIMIT ' . $limit;
        }
        
        $this->db->query($sql);
        $this->db->bind(':user_id', $user_id);
        return $this->db->resultSet();
    }

    public function getSummaryByUser($user_id) {
        $this->db->query('SELECT type, SUM(amount) as total FROM transactions WHERE user_id = :user_id GROUP BY type');
        $this->db->bind(':user_id', $user_id);
        $results = $this->db->resultSet();
        
        $summary = ['income' => 0, 'expense' => 0, 'balance' => 0];
        foreach ($results as $row) {
            if ($row->type == 'income') {
                $summary['income'] = $row->total;
            } elseif ($row->type == 'expense') {
                $summary['expense'] = $row->total;
            }
        }
        $summary['balance'] = $summary['income'] - $summary['expense'];
        return $summary;
    }

    public function addTransaction($data) {
        $this->db->query('INSERT INTO transactions (user_id, category_id, amount, type, transaction_date, description) VALUES (:user_id, :category_id, :amount, :type, :transaction_date, :description)');
        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':category_id', $data['category_id']);
        $this->db->bind(':amount', $data['amount']);
        $this->db->bind(':type', $data['type']);
        $this->db->bind(':transaction_date', $data['transaction_date']);
        $this->db->bind(':description', $data['description']);

        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function deleteTransaction($id, $user_id) {
        $this->db->query('DELETE FROM transactions WHERE id = :id AND user_id = :user_id');
        $this->db->bind(':id', $id);
        $this->db->bind(':user_id', $user_id);

        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }
}
