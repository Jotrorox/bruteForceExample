<?php
// Set the correct content type for JSON
header('Content-Type: application/json');

// Path to the users file
$usersFile = 'users.txt';

// Initialize an array to store user credentials
$userData = [];

// Read the users file
if (file_exists($usersFile)) {
    $file = fopen($usersFile, 'r');
    while (($line = fgetcsv($file, 1000, ':')) !== false) {
        // Add username and password to the array
        $userData[] = [
            'username' => $line[0],
            'password' => $line[1]
        ];
    }
    fclose($file);
} else {
    echo json_encode(['error' => 'Users file not found.']);
    exit;
}

// Return the user data as a JSON response
echo json_encode($userData, JSON_PRETTY_PRINT);
?>
