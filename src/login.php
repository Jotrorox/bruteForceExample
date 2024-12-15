<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Read user data from a file
    $users = [];
    $file = fopen('rsc/users.txt', 'r');
    while (($line = fgetcsv($file, 1000, ':')) !== false) {
        $users[$line[0]] = $line[1];
    }
    fclose($file);

    if (isset($users[$username]) && trim($users[$username]) === trim($password)) {
        $_SESSION['username'] = $username;
        echo '<div id="user-name-value" class="d-none">' . htmlspecialchars($username) . '</div>';
        echo '<div class="alert alert-success">Login successful! Welcome, ' . htmlspecialchars($username) . '.</div>';
    } else {
        echo '<div class="alert alert-danger">Invalid username or password. Please try again.</div>';
    }
} else {
    echo '<div class="alert alert-danger">Invalid request method.</div>';
}
?>
