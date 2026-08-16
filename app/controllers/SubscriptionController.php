<?php
require_once APPROOT . '/../vendor/autoload.php';

class SubscriptionController extends Controller {
    private $userModel;

    public function __construct() {
        $this->userModel = $this->model('User');
    }

    public function upgrade() {
        if (!isLoggedIn()) {
            header('Location: ' . URLROOT . '/auth/login');
            exit;
        }

        $user = $this->userModel->getUserById($_SESSION['user_id']);
        if ($user->subscription_tier === 'premium') {
            flash('transaction_message', 'You are already on the Premium tier!');
            header('Location: ' . URLROOT . '/dashboard');
            exit;
        }

        \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

        try {
            $checkout_session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => 'Premium Subscription - Daily Expense Tracker',
                        ],
                        'unit_amount' => 500, // $5.00
                        'recurring' => [
                            'interval' => 'month',
                        ],
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'subscription',
                'success_url' => URLROOT . '/subscription/success?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => URLROOT . '/subscription/cancel',
                'customer_email' => $user->email,
                'metadata' => [
                    'user_id' => $user->id
                ]
            ]);

            header("HTTP/1.1 303 See Other");
            header("Location: " . $checkout_session->url);
            exit;
        } catch (\Exception $e) {
            flash('transaction_message', 'Error initiating checkout: ' . $e->getMessage(), 'alert alert-danger');
            header('Location: ' . URLROOT . '/dashboard');
            exit;
        }
    }

    public function success() {
        if (!isLoggedIn()) {
            header('Location: ' . URLROOT . '/auth/login');
            exit;
        }
        $data = ['title' => 'Subscription Successful'];
        $this->view('subscription/success', $data);
    }

    public function cancel() {
        if (!isLoggedIn()) {
            header('Location: ' . URLROOT . '/auth/login');
            exit;
        }
        $data = ['title' => 'Subscription Cancelled'];
        $this->view('subscription/cancel', $data);
    }
}
