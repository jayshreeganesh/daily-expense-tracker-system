<?php
/**
 * PHP Built-in Server Router Script
 * This file emulates Apache's mod_rewrite functionality for the built-in PHP web server.
 */

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Serve existing files in the public directory
if ($uri !== '/' && file_exists(__DIR__.'/public'.$uri)) {
    if (pathinfo($uri, PATHINFO_EXTENSION) === 'php') {
        require_once __DIR__.'/public'.$uri;
        exit;
    }
    return false;
}

// Emulate the rewrite rule by setting $_GET['url']
$_GET['url'] = ltrim($uri, '/');

// Include the front controller
require_once __DIR__.'/public/index.php';
