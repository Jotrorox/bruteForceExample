<?php
// Base application path
define('APP_ROOT', getenv('APPLICATION_ROOT') ?: '/var/www/brute-force-demo');
define('DATA_PATH', APP_ROOT . '/data');
define('INCLUDES_PATH', APP_ROOT . '/includes');

// Security settings
define('SECURE_SESSION', true);
define('SESSION_NAME', 'brute_force_demo');

// Initialize secure session
function init_secure_session() {
    if (SECURE_SESSION) {
        ini_set('session.cookie_httponly', 1);
        ini_set('session.cookie_secure', 1);
        ini_set('session.use_only_cookies', 1);
        ini_set('session.cookie_samesite', 'Strict');
    }
    
    session_name(SESSION_NAME);
}

// Initialize session settings
init_secure_session(); 