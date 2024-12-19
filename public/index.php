<?php
require_once '../config/config.php';
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brute Force Example Login</title>
    <meta name="description" content="A simple login page with a brute force example.">
    <meta name="keywords" content="login, brute force, example, security, login page, jotrorox">
    <meta name="author" content="Johannes (Jotrorox) Müller - https://jotrorox.com">
    <meta name="robots" content="index, follow">
    <meta name="theme-color" content="#6d28d9">
    <meta name="msapplication-TileColor" content="#6d28d9">
    <meta name="msapplication-TileImage" content="/favicon.ico">
    <meta name="msapplication-config" content="/browserconfig.xml">
    <meta name="apple-mobile-web-app-title" content="Brute Force Example Login">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Brute Force Example Login">
    <script src="https://unpkg.com/htmx.org@2.0.4"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.98); }
            to { opacity: 1; transform: scale(1); }
        }
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fade-in {
            animation: fadeIn 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .slide-down {
            animation: slideDown 0.5s cubic-bezier(0.16, 1, 0.3, 1);
        }
        body {
            font-family: 'Nunito', sans-serif;
        }
        @keyframes iconFloat {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-3px); }
        }
        
        #darkModeToggle:hover #darkModeIcon {
            animation: iconFloat 1s ease-in-out infinite;
        }
        
        /* Add responsive font sizes */
        @media (max-width: 640px) {
            h2 {
                font-size: 1.875rem !important;
            }
            .text-lg {
                font-size: 1.125rem !important;
            }
        }
        
        /* Improve touch targets on mobile */
        @media (max-width: 640px) {
            button, input {
                min-height: 48px;
            }
        }

        .dots-container {
            position: absolute;
            inset: -50px;
            pointer-events: none;
        }

        .dot {
            position: absolute;
            border-radius: 50%;
            background: linear-gradient(to right, rgba(99, 102, 241, 0.15), rgba(168, 85, 247, 0.15));
            backdrop-filter: blur(1px);
            animation: float var(--duration) ease-in-out infinite;
            animation-delay: var(--delay);
        }

        .dark .dot {
            background: linear-gradient(to right, rgba(99, 102, 241, 0.07), rgba(168, 85, 247, 0.07));
        }

        @keyframes float {
            0%, 100% {
                transform: translate(0, 0) scale(1);
            }
            50% {
                transform: translate(var(--translate-x), var(--translate-y)) scale(1.05);
            }
        }

        /* Improve mobile touch targets */
        @media (max-width: 640px) {
            #darkModeToggle {
                width: 3.5rem;
                height: 3.5rem;
            }
        }

        /* Floating action buttons */
        .fab-container {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            display: flex;
            flex-direction: row;
            gap: 1rem;
            z-index: 50;
        }

        @media (min-width: 640px) {
            .fab-container {
                flex-direction: column;
            }
        }

        .fab {
            width: 3.5rem;
            height: 3.5rem;
            border-radius: 9999px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .fab:hover {
            transform: scale(1.05);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        /* Improve mobile touch targets */
        @media (max-width: 640px) {
            .fab {
                width: 4rem;
                height: 4rem;
            }
            
            .fab svg {
                width: 1.75rem;
                height: 1.75rem;
            }
        }

        /* Updated Modal styles */
        .modal {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 100;
            background-color: rgba(0, 0, 0, 0);
            backdrop-filter: blur(0px);
            transition: background-color 0.3s ease-out,
                        backdrop-filter 0.3s ease-out;
        }

        .modal.show {
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
        }

        .modal-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -40%);
            opacity: 0;
            min-width: 300px;
            max-width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1),
                        opacity 0.3s ease-out;
        }

        .modal.show .modal-content {
            transform: translate(-50%, -50%);
            opacity: 1;
        }

        /* Enhanced input field effects */
        .input-wrapper {
            position: relative;
            width: 100%;
        }

        .input-wrapper::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 0.75rem;
            padding: 2px;
            background: linear-gradient(90deg, #6366f1, #a855f7);
            -webkit-mask: linear-gradient(#fff 0 0) content-box, 
                          linear-gradient(#fff 0 0);
            mask: linear-gradient(#fff 0 0) content-box, 
                  linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .input-wrapper:focus-within::before {
            opacity: 1;
        }

        .form-input {
            position: relative;
            background: transparent;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .form-input:focus {
            transform: translateY(-1px);
            box-shadow: 0 4px 20px -8px rgba(99, 102, 241, 0.5);
        }

        /* Credential item hover effect */
        .credential-item {
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .credential-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px -8px rgba(99, 102, 241, 0.5);
        }

        .credential-item:active {
            transform: translateY(0px);
        }

        /* Enhanced dark mode toggle */
        .theme-toggle {
            position: relative;
            width: 3rem;
            height: 3rem;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 9999px;
            backdrop-filter: blur(8px);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
        }

        .dark .theme-toggle {
            background: rgba(31, 41, 55, 0.8);
        }

        .theme-toggle::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 9999px;
            padding: 1px;
            background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
            -webkit-mask: linear-gradient(#fff 0 0) content-box, 
                          linear-gradient(#fff 0 0);
            mask: linear-gradient(#fff 0 0) content-box, 
                  linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .theme-toggle:hover::before {
            opacity: 1;
        }

        .theme-toggle:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px -4px rgba(99, 102, 241, 0.3);
        }

        .theme-toggle:active {
            transform: translateY(0);
        }

        .theme-icon {
            position: relative;
            width: 1.5rem;
            height: 1.5rem;
            transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .theme-icon .sun,
        .theme-icon .moon {
            position: absolute;
            inset: 0;
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .theme-icon .sun {
            color: #f59e0b;
            opacity: 1;
            transform: rotate(0deg) scale(1);
        }

        .theme-icon .moon {
            color: #818cf8;
            opacity: 0;
            transform: rotate(90deg) scale(0.5);
        }

        .dark .theme-icon .sun {
            opacity: 0;
            transform: rotate(-90deg) scale(0.5);
        }

        .dark .theme-icon .moon {
            opacity: 1;
            transform: rotate(0deg) scale(1);
        }

        /* Add ripple effect */
        .ripple {
            position: absolute;
            border-radius: 50%;
            transform: scale(0);
            animation: ripple 0.6s linear;
            background-color: rgba(255, 255, 255, 0.3);
        }

        @keyframes ripple {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }

        /* Update the scrollbar styles for a more modern look */
        .custom-scrollbar {
            scrollbar-width: thin;
            scrollbar-color: rgba(99, 102, 241, 0.3) transparent;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
            height: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
            margin: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: linear-gradient(to bottom, rgba(99, 102, 241, 0.5), rgba(168, 85, 247, 0.5));
            border-radius: 100vh;
            border: 1px solid transparent;
            background-clip: padding-box;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(to bottom, rgba(99, 102, 241, 0.7), rgba(168, 85, 247, 0.7));
        }

        .dark .custom-scrollbar::-webkit-scrollbar-thumb {
            background: linear-gradient(to bottom, rgba(99, 102, 241, 0.3), rgba(168, 85, 247, 0.3));
        }

        .dark .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(to bottom, rgba(99, 102, 241, 0.5), rgba(168, 85, 247, 0.5));
        }
    </style>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    boxShadow: {
                        'custom': '0 0 50px -12px rgba(0, 0, 0, 0.25)',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gradient-to-br from-blue-50/50 via-indigo-50/50 to-purple-50/50 
    dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 
    min-h-screen flex items-center justify-center 
    transition-colors duration-300">
    <div class="fixed inset-0 -z-10 overflow-hidden">
        <div class="absolute inset-0" id="particles-background">
            <div class="dots-container"></div>
        </div>
    </div>
    <div class="absolute top-4 right-4 z-10">
        <button id="darkModeToggle" 
            class="theme-toggle flex items-center justify-center focus:outline-none"
            aria-label="Toggle dark mode">
            <div class="theme-icon">
                <svg class="sun" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"/>
                </svg>
                <svg class="moon" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/>
                </svg>
            </div>
        </button>
    </div>
    <div class="w-full max-w-md p-6 sm:p-8 
        bg-white/90 dark:bg-gray-800/90 
        backdrop-blur-md dark:text-gray-100 
        shadow-custom rounded-2xl 
        fade-in mx-4 my-8 sm:my-4">
        <h2 class="text-3xl font-bold text-center bg-gradient-to-r from-indigo-600 to-purple-600 dark:from-indigo-400 dark:to-purple-400 bg-clip-text text-transparent mb-8">Welcome Back</h2>
        <div id="login-form-container">
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
                            class="form-input mt-1 block w-full p-3 sm:p-3.5 border border-gray-300 
                                dark:border-gray-600 rounded-xl shadow-sm 
                                focus:outline-none
                                dark:focus:border-indigo-400 
                                bg-white/70 dark:bg-gray-700/70 
                                text-gray-900 dark:text-gray-100
                                placeholder-gray-400 dark:placeholder-gray-500
                                transition-all duration-200
                                text-base sm:text-sm">
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
                    class="w-full bg-gradient-to-r from-indigo-500 to-purple-600 dark:from-indigo-500 dark:to-purple-600 
                    text-white py-3 px-6 rounded-xl shadow-lg 
                    hover:shadow-xl hover:scale-[1.02] 
                    active:scale-[0.98]
                    transition-all duration-200 
                    focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400">
                    Sign In
                </button>
            </form>
        </div>
        <div id="logout-container" class="mt-6 hidden slide-down">
            <div class="text-center">
                <p class="text-gray-700 dark:text-gray-300 text-lg">Welcome back, <span id="user-name" class="font-semibold text-indigo-600 dark:text-indigo-400"></span>!</p>
                <button id="logout-button" 
                    class="mt-6 bg-gradient-to-r from-red-500 to-pink-600 
                    text-white py-3 px-8 rounded-xl shadow-lg 
                    hover:shadow-xl hover:scale-[1.02] 
                    active:scale-[0.98]
                    transition-all duration-200 
                    focus:outline-none focus:ring-2 focus:ring-red-500" 
                    hx-post="/includes/logout.php" 
                    hx-target="body"
                    hx-swap="outerHTML">
                    Sign Out
                </button>
            </div>
        </div>
    </div>
    <div class="fab-container">
        <button onclick="showLoginData()" 
            class="fab bg-gradient-to-r from-indigo-500 to-purple-600 text-white"
            title="Show example login data">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </button>
        <button onclick="showBruteForceSimulator()" 
            class="fab bg-gradient-to-r from-rose-500 to-red-600 text-white"
            title="Brute Force Simulator">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
        </button>
    </div>
    <div id="loginDataModal" class="modal">
        <div class="modal-content bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-xl">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Example Login Data</h3>
                <button onclick="closeModal('loginDataModal')" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div id="loginDataContent" class="space-y-4">
                <div class="animate-pulse">Loading...</div>
            </div>
        </div>
    </div>
    <div id="bruteForceModal" class="modal">
        <div class="modal-content bg-white dark:bg-gray-800 rounded-2xl p-4 sm:p-6 shadow-xl 
            max-w-2xl w-[95%] sm:w-full mx-auto">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white">Brute Force Simulator</h3>
                <button onclick="closeModal('bruteForceModal')" 
                    class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="space-y-4">
                <div class="flex flex-col sm:flex-row sm:space-x-4 space-y-4 sm:space-y-0">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Target Username</label>
                        <div class="input-wrapper">
                            <input type="text" 
                                id="bruteforce-username" 
                                class="form-input mt-1 block w-full p-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm 
                                    focus:outline-none
                                    dark:focus:border-indigo-400 
                                    bg-white/70 dark:bg-gray-700/70 
                                    text-gray-900 dark:text-gray-100
                                    transition-all duration-200" 
                                value="admin">
                        </div>
                    </div>
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Speed (ms)</label>
                        <div class="input-wrapper relative">
                            <input type="number" 
                                id="bruteforce-speed" 
                                class="form-input mt-1 block w-full p-3 pl-12 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm 
                                    focus:outline-none
                                    dark:focus:border-indigo-400 
                                    bg-white/70 dark:bg-gray-700/70 
                                    text-gray-900 dark:text-gray-100
                                    transition-all duration-200" 
                                value="100" 
                                min="10" 
                                max="1000"
                                step="10">
                            <div class="absolute inset-y-0 left-0 mt-1 flex items-center pl-3 pointer-events-none text-gray-500 dark:text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Password List</label>
                    <div class="input-wrapper">
                        <textarea id="bruteforce-passwords" 
                            rows="4" 
                            class="form-input mt-1 block w-full p-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm 
                                focus:outline-none
                                dark:focus:border-indigo-400 
                                bg-white/70 dark:bg-gray-700/70 
                                text-gray-900 dark:text-gray-100
                                font-mono text-sm
                                transition-all duration-200
                                resize-none"
                            placeholder="Enter passwords, one per line"></textarea>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row justify-between items-center space-y-4 sm:space-y-0">
                    <div class="flex space-x-2 w-full sm:w-auto">
                        <button onclick="startBruteForce()" 
                            id="start-bruteforce"
                            class="flex-1 sm:flex-none px-6 py-3 bg-gradient-to-r from-rose-500 to-red-600 text-white rounded-xl shadow-lg 
                            hover:shadow-xl hover:scale-[1.02] 
                            active:scale-[0.98]
                            transition-all duration-200
                            disabled:opacity-50 disabled:cursor-not-allowed">
                            Start Attack
                        </button>
                        <button onclick="stopBruteForce()" 
                            id="stop-bruteforce"
                            class="flex-1 sm:flex-none px-6 py-3 bg-gray-500 text-white rounded-xl shadow-lg 
                            hover:shadow-xl hover:scale-[1.02] 
                            active:scale-[0.98]
                            transition-all duration-200
                            disabled:opacity-50 disabled:cursor-not-allowed" 
                            disabled>
                            Stop
                        </button>
                    </div>
                    <button onclick="loadCommonPasswords()" 
                        class="w-full sm:w-auto px-6 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-xl shadow-lg 
                        hover:shadow-xl hover:scale-[1.02] 
                        active:scale-[0.98]
                        transition-all duration-200">
                        Load Common Passwords
                    </button>
                </div>

                <div class="mt-6">
                    <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400 mb-2">
                        <span>Progress:</span>
                        <span id="bruteforce-progress">0/0 (0%)</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3 overflow-hidden">
                        <div id="bruteforce-progress-bar" 
                            class="bg-gradient-to-r from-rose-500 to-red-600 h-full rounded-full transition-all duration-200 ease-out"
                            style="width: 0%"></div>
                    </div>
                </div>

                <div id="bruteforce-log" 
                    class="mt-6 p-4 bg-gray-100 dark:bg-gray-900 rounded-xl h-48 overflow-y-auto font-mono text-sm
                    border border-gray-200 dark:border-gray-700 custom-scrollbar">
                </div>
            </div>
        </div>
    </div>
    <script>
        const darkModeToggle = document.getElementById('darkModeToggle');
        const htmlElement = document.documentElement;

        // Check system preference initially
        if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
            htmlElement.classList.add('dark');
        }

        // Check localStorage for theme preference and apply it
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme) {
            if (savedTheme === 'dark') {
                htmlElement.classList.add('dark');
            } else {
                htmlElement.classList.remove('dark');
            }
        }

        // Toggle dark mode on button click with smooth transition
        darkModeToggle.addEventListener('click', () => {
            htmlElement.style.transition = 'background-color 0.5s ease';
            
            if (htmlElement.classList.contains('dark')) {
                htmlElement.classList.remove('dark');
                localStorage.setItem('theme', 'light');
            } else {
                htmlElement.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            }
            
            // Remove transition after animation completes
            setTimeout(() => {
                htmlElement.style.transition = '';
            }, 500);
        });

        // Listen for system theme changes
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
            if (!localStorage.getItem('theme')) {
                if (e.matches) {
                    htmlElement.classList.add('dark');
                } else {
                    htmlElement.classList.remove('dark');
                }
            }
        });

        // Modify createDots function to set initial positions in pixels instead of percentages
        function createDots() {
            const container = document.querySelector('.dots-container');
            const dotsCount = window.innerWidth < 768 ? 15 : 25;
            
            for (let i = 0; i < dotsCount; i++) {
                const dot = document.createElement('div');
                dot.className = 'dot';
                
                const size = Math.random() * 100 + 50;
                dot.style.width = `${size}px`;
                dot.style.height = `${size}px`;
                
                // Use pixels instead of percentages for position
                const left = Math.random() * container.offsetWidth;
                const top = Math.random() * container.offsetHeight;
                dot.style.left = `${left}px`;
                dot.style.top = `${top}px`;
                
                dot.style.setProperty('--duration', `${Math.random() * 10 + 10}s`);
                dot.style.setProperty('--delay', `-${Math.random() * 10}s`);
                dot.style.setProperty('--translate-x', `${(Math.random() - 0.5) * 50}px`);
                dot.style.setProperty('--translate-y', `${(Math.random() - 0.5) * 50}px`);
                
                container.appendChild(dot);
            }
        }

        // Create dots when page loads
        createDots();

        // Recreate dots when window is resized
        let resizeTimeout;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(() => {
                const container = document.querySelector('.dots-container');
                container.innerHTML = '';
                createDots();
            }, 250);
        });

        document.addEventListener('click', (e) => {
            // Don't spawn dots if clicking on or inside a modal or button
            if (e.target.closest('.modal') || 
                e.target.closest('button') || 
                e.target.closest('.fab') ||
                e.target.closest('input') ||
                e.target.closest('textarea')) {
                return;
            }
            
            const dot = document.createElement('div');
            dot.className = 'dot';
            
            // Random size between 50px and 150px
            const size = Math.random() * 100 + 50;
            dot.style.width = `${size}px`;
            dot.style.height = `${size}px`;
            
            // Position relative to click with offset for dot size
            const rect = document.querySelector('.dots-container').getBoundingClientRect();
            const left = (e.clientX - rect.left - size/2);
            const top = (e.clientY - rect.top - size/2);
            dot.style.left = `${left}px`;
            dot.style.top = `${top}px`;
            
            // Add animation properties
            dot.style.setProperty('--duration', `${Math.random() * 10 + 10}s`);
            dot.style.setProperty('--delay', '0s');
            dot.style.setProperty('--translate-x', `${(Math.random() - 0.5) * 50}px`);
            dot.style.setProperty('--translate-y', `${(Math.random() - 0.5) * 50}px`);
            
            document.querySelector('.dots-container').appendChild(dot);
        });

        // Function to show login data modal
        async function showLoginData() {
            const modal = document.getElementById('loginDataModal');
            const content = document.getElementById('loginDataContent');
            modal.style.display = 'block';
            modal.offsetHeight;
            modal.classList.add('show');
            
            try {
                const response = await fetch('/includes/get_passwords.php');
                const data = await response.json();
                
                content.innerHTML = `
                    <div class="space-y-4">
                        ${data.map(user => `
                            <div class="credential-item p-4 bg-gray-50 dark:bg-gray-700 rounded-lg
                                hover:bg-gray-100 dark:hover:bg-gray-600" 
                                onclick="fillCredentials('${user.username}', '${user.password}')">
                                <div class="font-medium text-gray-700 dark:text-gray-300">
                                    Username: ${user.username}
                                </div>
                                <div class="text-gray-600 dark:text-gray-400">
                                    Password: ${user.password}
                                </div>
                            </div>
                        `).join('')}
                    </div>
                `;
            } catch (error) {
                content.innerHTML = `
                    <div class="text-red-500 dark:text-red-400">
                        Error loading login data. Please try again later.
                    </div>
                `;
            }
        }

        // Add the fillCredentials function
        function fillCredentials(username, password) {
            const usernameInput = document.getElementById('username');
            const passwordInput = document.getElementById('password');
            
            // Animate the inputs
            usernameInput.style.transform = 'scale(1.02)';
            passwordInput.style.transform = 'scale(1.02)';
            
            // Fill the credentials
            usernameInput.value = username;
            passwordInput.value = password;
            
            // Reset the animation
            setTimeout(() => {
                usernameInput.style.transform = '';
                passwordInput.style.transform = '';
            }, 200);
            
            // Close the modal with a slight delay
            setTimeout(() => {
                closeModal('loginDataModal');
            }, 300);
        }

        // Function to show message modal
        function showMessage(message) {
            const modal = document.createElement('div');
            modal.className = 'modal';
            modal.innerHTML = `
                <div class="modal-content bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-xl max-w-sm">
                    <div class="text-center">
                        <p class="text-gray-700 dark:text-gray-300 mb-4">${message}</p>
                        <button onclick="closeModal(this.closest('.modal').id)" 
                            class="px-4 py-2 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-lg
                            hover:shadow-lg transition-all duration-200">
                            OK
                        </button>
                    </div>
                </div>
            `;
            modal.id = 'message-modal-' + Date.now();
            document.body.appendChild(modal);
            modal.style.display = 'block';
            // Trigger reflow
            modal.offsetHeight;
            modal.classList.add('show');
        }

        // Function to close modal
        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.classList.remove('show');
            
            // Wait for the animation to complete before hiding
            setTimeout(() => {
                modal.style.display = 'none';
                // If it's a message modal, remove it from the DOM
                if (modalId.startsWith('message-modal-')) {
                    modal.remove();
                }
            }, 300); // Match the transition duration
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                closeModal(event.target.id);
            }
        }

        // Add ripple effect to theme toggle
        darkModeToggle.addEventListener('click', function(e) {
            const button = e.currentTarget;
            const ripple = document.createElement('div');
            const rect = button.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.width = ripple.style.height = `${size}px`;
            ripple.style.left = `${x}px`;
            ripple.style.top = `${y}px`;
            ripple.className = 'ripple';
            
            button.appendChild(ripple);
            
            ripple.addEventListener('animationend', () => {
                ripple.remove();
            });
        });

        let bruteforceInterval;
        let passwords = [];
        let currentIndex = 0;
        let targetCredentials = null;

        async function showBruteForceSimulator() {
            const modal = document.getElementById('bruteForceModal');
            modal.style.display = 'block';
            modal.offsetHeight;
            modal.classList.add('show');
            
            // Fetch the actual passwords for comparison
            try {
                const response = await fetch('/includes/get_passwords.php');
                targetCredentials = await response.json();
            } catch (error) {
                console.error('Error loading passwords:', error);
            }
        }

        function loadCommonPasswords() {
            const commonPasswords = [
                'password123', '123456', 'admin123', 'qwerty',
                'letmein', 'welcome', 'monkey123', 'football',
                'abc123', 'password1', '123456789', 'admin',
                'test123', '12345', 'password', 'root',
                'userpass', 'doepass'
            ];
            document.getElementById('bruteforce-passwords').value = commonPasswords.join('\n');
        }

        function addLogEntry(message, type = 'info') {
            const log = document.getElementById('bruteforce-log');
            const entry = document.createElement('div');
            entry.className = `py-1 ${type === 'success' ? 'text-green-600 dark:text-green-400' : 
                                type === 'error' ? 'text-red-600 dark:text-red-400' : 
                                'text-gray-600 dark:text-gray-400'}`;
            entry.textContent = `[${new Date().toLocaleTimeString()}] ${message}`;
            log.appendChild(entry);
            log.scrollTop = log.scrollHeight;
        }

        function updateProgress() {
            const total = passwords.length;
            const current = Math.min(currentIndex, total); // Ensure current doesn't exceed total
            
            // Only show progress if there are passwords
            if (total === 0) {
                document.getElementById('bruteforce-progress').textContent = '0/0 (0%)';
                document.getElementById('bruteforce-progress-bar').style.width = '0%';
                return;
            }
            
            // If we've reached the end, show total/total
            if (currentIndex >= total) {
                document.getElementById('bruteforce-progress').textContent = `${total}/${total} (100%)`;
                document.getElementById('bruteforce-progress-bar').style.width = '100%';
                return;
            }
            
            // Otherwise show current progress
            const percentage = Math.round((current / total) * 100);
            document.getElementById('bruteforce-progress').textContent = 
                `${current}/${total} (${percentage}%)`;
            document.getElementById('bruteforce-progress-bar').style.width = `${percentage}%`;
        }

        function startBruteForce() {
            const username = document.getElementById('bruteforce-username').value;
            passwords = document.getElementById('bruteforce-passwords').value
                .split('\n')
                .filter(p => p.trim());
            
            if (!passwords.length) {
                addLogEntry('No passwords provided!', 'error');
                return;
            }

            currentIndex = 0;
            document.getElementById('start-bruteforce').disabled = true;
            document.getElementById('stop-bruteforce').disabled = false;
            document.getElementById('bruteforce-log').innerHTML = '';
            document.getElementById('bruteforce-progress-bar').style.backgroundColor = ''; // Reset color
            
            addLogEntry(`Starting brute force attack on username: ${username}`);
            updateProgress(); // Update initial progress
            
            bruteforceInterval = setInterval(() => {
                const currentPassword = passwords[currentIndex];
                addLogEntry(`Trying password: ${currentPassword}`);

                // Check if the password matches any user
                const match = targetCredentials.find(
                    cred => cred.username === username && cred.password === currentPassword
                );

                if (match) {
                    stopBruteForce();
                    addLogEntry(`Success! Password found: ${currentPassword}`, 'success');
                    // Animate the success
                    document.getElementById('bruteforce-progress-bar').style.backgroundColor = '#22c55e';
                }

                currentIndex++;
                updateProgress();

                // Check if we're done after updating progress
                if (currentIndex >= passwords.length) {
                    stopBruteForce();
                    addLogEntry('Attack finished - Password not found', 'error');
                }
            }, parseInt(document.getElementById('bruteforce-speed').value));
        }

        function stopBruteForce() {
            clearInterval(bruteforceInterval);
            document.getElementById('start-bruteforce').disabled = false;
            document.getElementById('stop-bruteforce').disabled = true;
        }
    </script>
</body>
</html>