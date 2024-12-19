<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Debug output
    error_log("POST request received");
    error_log("POST data: " . print_r($_POST, true));
    
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Debug output
    error_log("Username: $username");
    error_log("Password: $password");

    // Read user data from a file
    $users = [];
    $file = fopen('users.txt', 'r');
    if ($file) {
        while (($line = fgetcsv($file, 1000, ':')) !== false) {
            $users[$line[0]] = $line[1];
        }
        fclose($file);
        
        // Debug output
        error_log("Users loaded: " . print_r($users, true));

        if (isset($users[$username]) && trim($users[$username]) === trim($password)) {
            $_SESSION['username'] = $username;
            // Hide login form and show logout section
            echo '<script>
                document.getElementById("login-form").style.display = "none";
                document.getElementById("logout-container").classList.remove("hidden");
                document.getElementById("user-name").textContent = "' . htmlspecialchars($username) . '";
            </script>';
        } else {
            error_log("Authentication failed for user: $username");
            echo '<div class="p-4 mb-4 text-red-700 bg-red-100 rounded-lg dark:bg-red-200 dark:text-red-800" role="alert">
                Invalid username or password. Please try again.
            </div>';
        }
    } else {
        error_log("Could not open users.txt file");
        echo '<div class="p-4 mb-4 text-red-700 bg-red-100 rounded-lg dark:bg-red-200 dark:text-red-800" role="alert">
            System error. Please try again later.
        </div>';
    }
} else {
    error_log("Invalid request method: " . $_SERVER['REQUEST_METHOD']);
    echo '<div class="p-4 mb-4 text-red-700 bg-red-100 rounded-lg dark:bg-red-200 dark:text-red-800" role="alert">
        Invalid request method.
    </div>';
}
?>
