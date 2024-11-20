<?php
session_start();

// WARNING: This is an educational demonstration tool.
// DO NOT use these techniques against real systems.

// Target login credentials for demonstration
$DEMO_USER = "admin";
$DEMO_PASS = "password123";

// Initialize or get attempt counter
$attempts = $_SESSION['attempts'] ?? 0;
$success = false;
$lastAttempt = '';
$timeElapsed = 0;

function attemptLogin($username, $password): bool
{
    global $DEMO_USER, $DEMO_PASS;
    return ($username === $DEMO_USER && $password === $DEMO_PASS);
}

// Handle brute force attempt
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    if ($_POST['action'] === 'bruteforce') {
        $wordlist = explode("\n", trim($_POST['wordlist']));
        $username = $_POST['username'];

        $startTime = microtime(true);

        foreach ($wordlist as $password) {
            $password = trim($password);
            $attempts++;
            $lastAttempt = $password;

            if (attemptLogin($username, $password)) {
                $success = true;
                break;
            }
        }

        $timeElapsed = round(microtime(true) - $startTime, 2);
        $_SESSION['attempts'] = $attempts;
    } elseif ($_POST['action'] === 'reset') {
        $attempts = 0;
        $_SESSION['attempts'] = 0;
    }
}

// Sample password list
$defaultWordlist = "123456\npassword\nadmin\nqwerty\nletmein\npassword123\nwelcome\nadmin123";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Educational Brute Force Testing Tool</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
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

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="text"], textarea {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        textarea {
            height: 200px;
            font-family: monospace;
        }

        .button-group {
            margin-top: 15px;
        }

        button {
            padding: 10px 20px;
            margin-right: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .primary {
            background: #007bff;
            color: white;
        }

        .secondary {
            background: #6c757d;
            color: white;
        }

        .results {
            margin-top: 20px;
            padding: 15px;
            border-radius: 4px;
        }

        .success {
            background: #d4edda;
            color: #155724;
        }

        .failure {
            background: #f8d7da;
            color: #721c24;
        }

        .stats {
            margin-top: 15px;
            padding: 10px;
            background: #e9ecef;
            border-radius: 4px;
        }

        .info {
            background: #cce5ff;
            color: #004085;
            padding: 15px;
            margin: 10px 0;
            border-radius: 4px;
        }

        .navigation {
            margin-top: 20px;
            text-align: center;
        }

        .navigation a {
            color: #007bff;
            text-decoration: none;
            padding: 10px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="warning">
        <strong>⚠️ Educational Purpose Only</strong><br>
        This tool is for educational demonstration of security vulnerabilities.
        Never use these techniques against real systems without authorization.
    </div>

    <h2>Brute Force Testing Tool</h2>

    <div class="info">
        <strong>Demo Credentials:</strong><br>
        Username: admin<br>
        Password: password123
    </div>

    <form method="post">
        <input type="hidden" name="action" value="bruteforce">

        <div class="form-group">
            <label for="username">Target Username:</label>
            <input type="text" id="username" name="username" value="admin" required>
        </div>

        <div class="form-group">
            <label for="wordlist">Password List (one per line):</label>
            <textarea id="wordlist" name="wordlist"
                      required><?php echo htmlspecialchars($defaultWordlist); ?></textarea>
        </div>

        <div class="button-group">
            <button type="submit" class="primary">Start Attack Simulation</button>
            <button type="submit" name="action" value="reset" class="secondary">Reset</button>
        </div>
    </form>

    <?php if ($attempts > 0): ?>
        <div class="results <?php echo $success ? 'success' : 'failure'; ?>">
            <h3>Attack Simulation Results:</h3>
            <p><strong>Status:</strong> <?php echo $success ? 'Password Found!' : 'Password Not Found'; ?></p>
            <?php if ($success): ?>
                <p><strong>Successful Password:</strong> <?php echo htmlspecialchars($lastAttempt); ?></p>
            <?php endif; ?>
        </div>

        <div class="stats">
            <h3>Statistics:</h3>
            <p><strong>Total Attempts:</strong> <?php echo $attempts; ?></p>
            <p><strong>Last Attempted Password:</strong> <?php echo htmlspecialchars($lastAttempt); ?></p>
            <p><strong>Time Elapsed:</strong> <?php echo $timeElapsed; ?> seconds</p>
            <p><strong>Attempts per Second:</strong> <?php
                if ($timeElapsed > 0) {
                    echo round($attempts / $timeElapsed, 2);
                } else {
                    echo "N/A (too fast to measure)";
                }
                ?></p>
        </div>
    <?php endif; ?>

    <div class="info" style="margin-top: 20px;">
        <h3>Educational Notes:</h3>
        <ul>
            <li>This tool demonstrates why strong passwords and proper security measures are crucial</li>
            <li>Real systems should implement:
                <ul>
                    <li>Rate limiting</li>
                    <li>Account lockouts</li>
                    <li>CAPTCHA or similar challenges</li>
                    <li>Multi-factor authentication</li>
                    <li>Password hashing</li>
                </ul>
            </li>
            <li>Monitor the statistics to understand how quickly simple passwords can be compromised</li>
        </ul>
    </div>

    <div class="navigation">
        <a href="/index.php">Go to Login Page</a>
    </div>
</div>
</body>
</html>

