<?php
require_once '/var/www/brute-force-demo/config/config.php';
session_start();

session_unset();
session_destroy();
?>
<form id="login-form" method="POST" action="/includes/login.php" hx-post="/includes/login.php" hx-target="#login-form-container" class="space-y-6">
    <div>
        <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Username</label>
        <div class="input-wrapper">
            <input type="text" 
                id="username" 
                name="username" 
                required 
                accept="text" 
                autocomplete="username" 
                placeholder="admin"
                class="form-input mt-1 block w-full p-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm 
                    focus:outline-none
                    dark:focus:border-indigo-400 
                    bg-white/70 dark:bg-gray-700/70 
                    text-gray-900 dark:text-gray-100
                    placeholder-gray-400 dark:placeholder-gray-500
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
                accept="password" 
                autocomplete="current-password" 
                placeholder="password123"
                class="form-input mt-1 block w-full p-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm 
                    focus:outline-none
                    dark:focus:border-indigo-400 
                    bg-white/70 dark:bg-gray-700/70 
                    text-gray-900 dark:text-gray-100
                    placeholder-gray-400 dark:placeholder-gray-500
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