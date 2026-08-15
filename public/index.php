<?php
if (!file_exists(__DIR__ . '/../setup.lock')) {
    header('Location: setup.php');
    exit;
}

require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/helpers/session_helper.php';
require_once __DIR__ . '/../app/core/App.php';
require_once __DIR__ . '/../app/core/Controller.php';
require_once __DIR__ . '/../app/core/Database.php';

// Initialize the App (Router)
$init = new App();
