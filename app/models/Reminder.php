<?php
class Reminder {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function getRemindersByUser($user_id) {
        $this->db->query('SELECT * FROM reminders WHERE user_id = :user_id ORDER BY status ASC, due_date ASC');
        $this->db->bind(':user_id', $user_id);
        return $this->db->resultSet();
    }

    public function getPendingCount($user_id) {
        $this->db->query('SELECT COUNT(*) as total FROM reminders WHERE user_id = :user_id AND status = "pending"');
        $this->db->bind(':user_id', $user_id);
        return $this->db->single()->total;
    }

    public function addReminder($data) {
        $this->db->query('INSERT INTO reminders (user_id, title, amount, due_date) VALUES (:user_id, :title, :amount, :due_date)');
        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':amount', $data['amount']);
        $this->db->bind(':due_date', $data['due_date']);
        
        return $this->db->execute();
    }

    public function completeReminder($id, $user_id) {
        $this->db->query('UPDATE reminders SET status = "paid" WHERE id = :id AND user_id = :user_id');
        $this->db->bind(':id', $id);
        $this->db->bind(':user_id', $user_id);
        return $this->db->execute();
    }

    public function deleteReminder($id, $user_id) {
        $this->db->query('DELETE FROM reminders WHERE id = :id AND user_id = :user_id');
        $this->db->bind(':id', $id);
        $this->db->bind(':user_id', $user_id);
        return $this->db->execute();
    }
}
