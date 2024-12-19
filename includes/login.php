<?php
require_once '/var/www/brute-force-demo/config/config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Validate input
    if (empty($username) || empty($password)) {
        http_response_code(400);
        echo json_encode(['error' => 'Username and password are required']);
        exit;
    }

    // Read user data from the protected data directory
    $users = [];
    $file = fopen(DATA_PATH . '/users.txt', 'r');
    if ($file) {
        while (($line = fgetcsv($file, 1000, ':')) !== false) {
            $users[$line[0]] = $line[1];
        }
        fclose($file);

        if (isset($users[$username]) && trim($users[$username]) === trim($password)) {
            $_SESSION['username'] = $username;
            echo json_encode([
                'success' => true,
                'username' => htmlspecialchars($username)
            ]);
        } else {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid username or password']);
        }
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'System error']);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
} 