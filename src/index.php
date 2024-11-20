<?php
session_start();

require_once 'Navigation.php';
$navigation = new Navigation();

// WARNING: This is an educational demonstration of security vulnerabilities.
// DO NOT use this code in production - it intentionally lacks security measures.

// Demo credentials - in real apps, never store plain text passwords
$DEMO_USER = "admin";
$DEMO_PASS = "password123";

// Track login attempts for demonstration
$attempts = $_SESSION['attempts'] ?? 0;

function logAttempt($username, $success): void
{
    // Log attempts to demonstrate the vulnerability
    $timestamp = date('Y-m-d H:i:s');
    $result = $success? "SUCCESS" : "FAILED";
    error_log("[$timestamp] Login attempt for user '$username': $result");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Vulnerable authentication - no rate limiting or delays
    if ($username === $DEMO_USER && $password === $DEMO_PASS) {
        $_SESSION['logged_in'] = true;
        $_SESSION['attempts'] = 0;
        logAttempt($username, true);
    } else {
        $_SESSION['attempts'] = ++$attempts;
        logAttempt($username, false);
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit;
}

$isLoggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Security Demo - Vulnerable Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background: #f5f5f5;
        }

        .warning {
            background-color: #fff3cd;
            border: 1px solid #ffeeba;
            color: #856404;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        .container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .attempts {
            color: #721c24;
            margin: 10px 0;
        }

        form {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 4px;
        }

        input {
            width: 100%;
            padding: 8px;
            margin: 10px 0;
            box-sizing: border-box;
        }

        button {
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 4px;
        }

        .success {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 4px;
        }
    </style>
</head>
<body>
<?php echo $navigation->render(); ?>
<div class="container">
    <div class="warning">
        <strong>⚠️ Educational Purpose Only</strong><br>
        This application intentionally demonstrates security vulnerabilities.
        Never use this code in production environments.
    </div>

    <?php if ($isLoggedIn):?>
        <div class="success">
            <h2>Successfully Logged In!</h2>
            <p>You've accessed the secure area.</p>
            <button><a href="?logout" style="text-decoration: none; color: white;">Logout</a></button>
        </div>
    <?php else:?>
        <h2>Vulnerable Login Form</h2>
        <div class="attempts">
            Failed attempts: <?php echo $attempts;?>
        </div>
        <form method="post">
            <div>
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Login</button>
        </form>

        <h3>Security Vulnerabilities:</h3>
        <ul>
            <li>No rate limiting</li>
            <li>No account lockout</li>
            <li>No delay between attempts</li>
            <li>Plain text password storage</li>
            <li>Basic session management</li>
        </ul>
    <?php endif;?>
</div>
</body>
</html>
