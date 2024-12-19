<?php
require_once '/var/www/brute-force-demo/config/config.php';
header('Content-Type: application/json');

$usersFile = DATA_PATH . '/users.txt';
$userData = [];

if (file_exists($usersFile)) {
    $file = fopen($usersFile, 'r');
    while (($line = fgetcsv($file, 1000, ':')) !== false) {
        $userData[] = [
            'username' => $line[0],
            'password' => $line[1]
        ];
    }
    fclose($file);
    echo json_encode($userData);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Users file not found']);
} 