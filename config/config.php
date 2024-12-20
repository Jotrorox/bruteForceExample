<?php
// Initialize secure session first, before any output
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_samesite', 'Strict');
    session_name('brute_force_demo');
    session_start();
}

// Base application path
define('APP_ROOT', getenv('APPLICATION_ROOT') ?: '/var/www/brute-force-demo');
define('DATA_PATH', APP_ROOT . '/data');
define('INCLUDES_PATH', APP_ROOT . '/includes');

// Security settings
define('SECURE_SESSION', true);
define('SESSION_NAME', 'brute_force_demo');

// Performance optimizations - only set if not already set
ini_set('realpath_cache_size', '4096K');
ini_set('realpath_cache_ttl', 600);