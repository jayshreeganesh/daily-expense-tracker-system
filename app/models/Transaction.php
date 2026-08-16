<?php
class Transaction {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function getTransactionsByUser($user_id, $limit = null, $offset = 0, $month = null) {
        $sql = 'SELECT t.*, c.name as category_name, c.color_code 
                FROM transactions t 
                LEFT JOIN categories c ON t.category_id = c.id 
                WHERE t.user_id = :user_id';

        if ($month) {
            $sql .= ' AND DATE_FORMAT(t.transaction_date, "%Y-%m") = :month';
        }

        $sql .= ' ORDER BY t.transaction_date DESC, t.created_at DESC';

        if ($limit !== null) {
            $sql .= ' LIMIT :limit OFFSET :offset';
        }
        
        $this->db->query($sql);
        $this->db->bind(':user_id', $user_id);

        if ($month) {
            $this->db->bind(':month', $month);
        }
        if ($limit !== null) {
            $this->db->bind(':limit', (int)$limit, PDO::PARAM_INT);
            $this->db->bind(':offset', (int)$offset, PDO::PARAM_INT);
        }

        return $this->db->resultSet();
    }

    public function getTotalTransactionsByUser($user_id, $month = null) {
        $sql = 'SELECT COUNT(*) as total FROM transactions WHERE user_id = :user_id';
        if ($month) {
            $sql .= ' AND DATE_FORMAT(transaction_date, "%Y-%m") = :month';
        }
        $this->db->query($sql);
        $this->db->bind(':user_id', $user_id);
        if ($month) {
            $this->db->bind(':month', $month);
        }
        return $this->db->single()->total;
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

    public function getExpensesByCategory($user_id) {
        $this->db->query('SELECT c.name, c.color_code, SUM(t.amount) as total FROM transactions t JOIN categories c ON t.category_id = c.id WHERE t.user_id = :user_id AND t.type = "expense" GROUP BY c.id');
        $this->db->bind(':user_id', $user_id);
        return $this->db->resultSet();
    }

    public function addTransaction($data) {
        $this->db->query('INSERT INTO transactions (user_id, category_id, amount, original_currency, original_amount, exchange_rate, type, transaction_date, description) VALUES (:user_id, :category_id, :amount, :original_currency, :original_amount, :exchange_rate, :type, :transaction_date, :description)');
        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':category_id', $data['category_id']);
        $this->db->bind(':amount', $data['amount']);
        $this->db->bind(':original_currency', $data['original_currency'] ?? 'USD');
        $this->db->bind(':original_amount', $data['original_amount'] ?? $data['amount']);
        $this->db->bind(':exchange_rate', $data['exchange_rate'] ?? 1.0000);
        $this->db->bind(':type', $data['type']);
        $this->db->bind(':transaction_date', $data['transaction_date']);
        $this->db->bind(':description', $data['description']);

        if ($this->db->execute()) {
            $id = $this->db->lastInsertId();
            log_audit($data['user_id'], 'Created Transaction', 'Transaction', $id, 'Amount: ' . $data['amount']);
            return true;
        }
        return false;
    }

    public function deleteTransaction($id, $user_id) {
        $this->db->query('DELETE FROM transactions WHERE id = :id AND user_id = :user_id');
        $this->db->bind(':id', $id);
        $this->db->bind(':user_id', $user_id);

        if ($this->db->execute()) {
            log_audit($user_id, 'Deleted Transaction', 'Transaction', $id);
            return true;
        }
        return false;
    }
    public function getWeeklyTrends($user_id) {
        $this->db->query('SELECT DATE(transaction_date) as date, SUM(amount) as total FROM transactions WHERE user_id = :user_id AND type = "expense" AND transaction_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) GROUP BY DATE(transaction_date) ORDER BY date ASC');
        $this->db->bind(':user_id', $user_id);
        return $this->db->resultSet();
    }
}
