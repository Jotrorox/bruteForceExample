<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Read user data from a file
    $users = [];
    $file = fopen('users.txt', 'r');
    while (($line = fgetcsv($file, 1000, ':')) !== false) {
        $users[$line[0]] = $line[1];
    }
    fclose($file);

    if (isset($users[$username]) && trim($users[$username]) === trim($password)) {
        $_SESSION['username'] = $username;
        // Hide login form and show logout section
        echo '<script>
            document.getElementById("login-form").style.display = "none";
            document.getElementById("logout-container").classList.remove("hidden");
            document.getElementById("user-name").textContent = "' . htmlspecialchars($username) . '";
        </script>';
    } else {
        echo '<div class="p-4 mb-4 text-red-700 bg-red-100 rounded-lg dark:bg-red-200 dark:text-red-800" role="alert">
            Invalid username or password. Please try again.
        </div>';
    }
} else {
    echo '<div class="p-4 mb-4 text-red-700 bg-red-100 rounded-lg dark:bg-red-200 dark:text-red-800" role="alert">
        Invalid request method.
    </div>';
}
?>
