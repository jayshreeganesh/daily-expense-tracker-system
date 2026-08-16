<?php
require_once APPROOT . '/../vendor/autoload.php';

class WebhookController extends Controller {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function stripe() {
        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';
        $event = null;

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sig_header, STRIPE_WEBHOOK_SECRET
            );
        } catch(\UnexpectedValueException $e) {
            http_response_code(400);
            exit();
        } catch(\Stripe\Exception\SignatureVerificationException $e) {
            http_response_code(400);
            exit();
        }

        if ($event->type == 'checkout.session.completed') {
            $session = $event->data->object;
            
            $customer_id = $session->customer;
            $user_id = $session->metadata->user_id ?? null;

            if ($user_id) {
                $this->db->query('UPDATE users SET stripe_customer_id = :customer_id, subscription_status = "active", subscription_tier = "premium" WHERE id = :id');
                $this->db->bind(':customer_id', $customer_id);
                $this->db->bind(':id', $user_id);
                $this->db->execute();
            }
        } elseif ($event->type == 'customer.subscription.deleted') {
            $subscription = $event->data->object;
            $customer_id = $subscription->customer;
            
            $this->db->query('UPDATE users SET subscription_status = "canceled", subscription_tier = "free" WHERE stripe_customer_id = :customer_id');
            $this->db->bind(':customer_id', $customer_id);
            $this->db->execute();
        }

        http_response_code(200);
    }
}
