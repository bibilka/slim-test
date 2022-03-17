<?php

// Should be set to 0 in production
error_reporting(E_ALL);

// Should be set to '0' in production
ini_set('display_errors', '1');

// Timezone
date_default_timezone_set('Europe/Moscow');

// Settings
$settings = [];

// Path settings
$settings['root'] = dirname(__DIR__);

$isDebug = getenv('APP_DEBUG');

// Error Handling Middleware settings
$settings['error'] = [

    // Should be set to false in production
    'display_error_details' => $isDebug,

    // Parameter is passed to the default ErrorHandler
    // View in rendered output by enabling the "displayErrorDetails" setting.
    // For the console and unit tests we also disable it
    'log_errors' => $isDebug,

    // Display error details in error log
    'log_error_details' => $isDebug,
];

// Database settings
$settings['db'] = [
    'driver' => 'mysql',
    'host' => 'mysql',
    'database' => getenv('DB_DATABASE'),
    'username' => getenv('DB_USERNAME'),
    'password' => getenv('DB_PASSWORD'),
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
];

return $settings;