<?php
/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */

$env = getenv('APPLICATION_ENV') ? : 'production';
/**
 * should be configured in server
 */
/*if($env != 'production'){
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else{
    ini_set('display_errors', 0);
}*/
        
        
chdir(dirname(__DIR__));

// Decline static file requests back to the PHP built-in webserver
if (php_sapi_name() === 'cli-server' && is_file(__DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))) {
    return false;
}

// Setup autoloading
require 'init_autoloader.php';

// Run the application!
Zend\Mvc\Application::init(require 'config/application.config.php')->run();
