<?php
// DB Params
define('DB_HOST', 'YOUR_DATABASE_HOST'); // e.g. localhost
define('DB_USER', 'YOUR_DATABASE_USER'); // e.g. root
define('DB_PASS', 'YOUR_DATABASE_PASSWORD');
define('DB_NAME', 'YOUR_DATABASE_NAME');

// App Root
define('APPROOT', dirname(dirname(__FILE__)));

// URL Root
define('URLROOT', 'http://localhost/expense_tracker');

// Site Name
define('SITENAME', 'Daily Expense Tracker');

// Currency Symbol
define('CURRENCY_SYMBOL', '$');

// Timezone
define('TIMEZONE', 'UTC');
date_default_timezone_set(TIMEZONE);

// Brand Color
define('BRAND_COLOR', '#0d6efd');
