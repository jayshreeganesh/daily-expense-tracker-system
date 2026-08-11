<?php
// Define base URL and database connection variables
define('APPROOT', dirname(dirname(__FILE__)));
// Dynamically calculate URLROOT to support Apache, Nginx, and PHP Built-in Server
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));

// If running in a subfolder with Apache, it will include '/public'. Remove it for the base URL.
if (substr($scriptDir, -7) === '/public') {
    $scriptDir = substr($scriptDir, 0, -7);
} elseif ($scriptDir === '/') {
    $scriptDir = '';
}

define('URLROOT', $protocol . $host . $scriptDir);
define('SITENAME', 'Daily Expense Tracker Pro');

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'root');
define('DB_NAME', 'expense_tracker');
