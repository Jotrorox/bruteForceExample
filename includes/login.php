<?php
require_once '/var/www/brute-force-demo/config/config.php';

// Cache users data in memory
static $users = null;

function get_users() {
    global $users;
    if ($users === null) {
        $users = [];
        $file = fopen(DATA_PATH . '/users.txt', 'r');
        if ($file) {
            while (($line = fgetcsv($file, 1000, ':')) !== false) {
                $users[$line[0]] = $line[1];
            }
            fclose($file);
        }
    }
    return $users;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Use cached users data
    $users = get_users();
    
    // If no credentials are provided, just return the login form
    if (!isset($_POST['username']) && !isset($_POST['password']) && isset($_SESSION['username'])) {
        ?>
        <div class="text-center slide-down">
            <p class="text-gray-700 dark:text-gray-300 text-lg mb-2">Welcome back, <span class="font-semibold text-indigo-600 dark:text-indigo-400"><?= htmlspecialchars($_SESSION['username']) ?></span>!</p>
            <p class="text-gray-600 dark:text-gray-400 mb-6">You have successfully logged in.</p>
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <button hx-post="/includes/snake_game.php" 
                    hx-target="#login-form-container"
                    class="bg-gradient-to-r from-green-500 to-emerald-600 
                        text-white py-3 px-8 rounded-xl shadow-lg 
                        hover:shadow-xl hover:scale-[1.02] 
                        active:scale-[0.98]
                        transition-all duration-200 
                        focus:outline-none focus:ring-2 focus:ring-green-500">
                    Play Snake
                </button>
                <button hx-post="/includes/logout.php" 
                    hx-target="#login-form-container"
                    class="bg-gradient-to-r from-red-500 to-pink-600 
                        text-white py-3 px-8 rounded-xl shadow-lg 
                        hover:shadow-xl hover:scale-[1.02] 
                        active:scale-[0.98]
                        transition-all duration-200 
                        focus:outline-none focus:ring-2 focus:ring-red-500">
                    Sign Out
                </button>
            </div>
        </div>
        <?php
        exit;
    }

    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Validate input
    if (empty($username) || empty($password)) {
        http_response_code(400);
        ?>
        <form id="login-form" method="POST" action="/includes/login.php" hx-post="/includes/login.php" hx-target="#login-form-container" class="space-y-6">
            <!-- Re-display the login form -->
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Username</label>
                <div class="input-wrapper">
                    <input type="text" 
                        id="username" 
                        name="username" 
                        required 
                        class="form-input mt-1 block w-full p-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm 
                            focus:outline-none
                            dark:focus:border-indigo-400 
                            bg-white/70 dark:bg-gray-700/70 
                            text-gray-900 dark:text-gray-100
                            transition-all duration-200">
                </div>
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Password</label>
                <div class="input-wrapper">
                    <input type="password" 
                        id="password" 
                        name="password" 
                        required 
                        class="form-input mt-1 block w-full p-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm 
                            focus:outline-none
                            dark:focus:border-indigo-400 
                            bg-white/70 dark:bg-gray-700/70 
                            text-gray-900 dark:text-gray-100
                            transition-all duration-200">
                </div>
            </div>
            <button type="submit" 
                class="w-full bg-gradient-to-r from-indigo-500 to-purple-600 
                text-white py-3 px-6 rounded-xl shadow-lg 
                hover:shadow-xl hover:scale-[1.02] 
                active:scale-[0.98]
                transition-all duration-200 
                focus:outline-none focus:ring-2 focus:ring-indigo-500">
                Sign In
            </button>
        </form>
        <?php
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
            // Return the logged-in HTML content
            ?>
            <div class="text-center slide-down">
                <p class="text-gray-700 dark:text-gray-300 text-lg mb-2">Welcome back, <span class="font-semibold text-indigo-600 dark:text-indigo-400"><?= htmlspecialchars($username) ?></span>!</p>
                <p class="text-gray-600 dark:text-gray-400 mb-6">You have successfully logged in.</p>
                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <button hx-post="/includes/snake_game.php" 
                        hx-target="#login-form-container"
                        class="bg-gradient-to-r from-green-500 to-emerald-600 
                            text-white py-3 px-8 rounded-xl shadow-lg 
                            hover:shadow-xl hover:scale-[1.02] 
                            active:scale-[0.98]
                            transition-all duration-200 
                            focus:outline-none focus:ring-2 focus:ring-green-500">
                        Play Snake
                    </button>
                    <button hx-post="/includes/logout.php" 
                        hx-target="#login-form-container"
                        class="bg-gradient-to-r from-red-500 to-pink-600 
                            text-white py-3 px-8 rounded-xl shadow-lg 
                            hover:shadow-xl hover:scale-[1.02] 
                            active:scale-[0.98]
                            transition-all duration-200 
                            focus:outline-none focus:ring-2 focus:ring-red-500">
                        Sign Out
                    </button>
                </div>
            </div>
            <?php
        } else {
            http_response_code(401);
            ?>
            <div class="text-center slide-down">
                <p class="text-red-600 dark:text-red-400 mb-4">Invalid username or password</p>
                <form id="login-form" method="POST" action="/includes/login.php" hx-post="/includes/login.php" hx-target="#login-form-container" class="space-y-6">
                    <!-- Re-display the login form -->
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Username</label>
                        <div class="input-wrapper">
                            <input type="text" 
                                id="username" 
                                name="username" 
                                required 
                                value="<?= htmlspecialchars($username) ?>"
                                class="form-input mt-1 block w-full p-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm 
                                    focus:outline-none
                                    dark:focus:border-indigo-400 
                                    bg-white/70 dark:bg-gray-700/70 
                                    text-gray-900 dark:text-gray-100
                                    transition-all duration-200">
                        </div>
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Password</label>
                        <div class="input-wrapper">
                            <input type="password" 
                                id="password" 
                                name="password" 
                                required 
                                class="form-input mt-1 block w-full p-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm 
                                    focus:outline-none
                                    dark:focus:border-indigo-400 
                                    bg-white/70 dark:bg-gray-700/70 
                                    text-gray-900 dark:text-gray-100
                                    transition-all duration-200">
                        </div>
                    </div>
                    <button type="submit" 
                        class="w-full bg-gradient-to-r from-indigo-500 to-purple-600 
                        text-white py-3 px-6 rounded-xl shadow-lg 
                        hover:shadow-xl hover:scale-[1.02] 
                        active:scale-[0.98]
                        transition-all duration-200 
                        focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        Sign In
                    </button>
                </form>
            </div>
            <?php
        }
    } else {
        http_response_code(500);
        echo '<p class="text-red-600 dark:text-red-400 text-center">System error</p>';
    }
} else {
    http_response_code(405);
    echo '<p class="text-red-600 dark:text-red-400 text-center">Method not allowed</p>';
} 