<?php
require_once '/var/www/brute-force-demo/config/config.php';
session_start();

session_unset();
session_destroy();

echo json_encode([
    'success' => true,
    'message' => 'Logged out successfully'
]); 