<?php
require_once '/var/www/brute-force-demo/config/config.php';
session_start();

if (!isset($_SESSION['username'])) {
    http_response_code(403);
    exit('Not authorized');
}
?>
<div class="text-center p-4 slide-down">
    <div class="mb-6">
        <p class="text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 dark:from-indigo-400 dark:to-purple-400 bg-clip-text text-transparent mb-2">Snake Game</p>
        <div class="flex justify-center items-center gap-4">
            <p class="text-gray-600 dark:text-gray-400">Score: <span id="score" class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">0</span></p>
            <p class="text-gray-600 dark:text-gray-400">High Score: <span id="highScore" class="text-2xl font-bold text-purple-600 dark:text-purple-400">0</span></p>
        </div>
    </div>
    
    <div class="relative">
        <div class="absolute inset-0 bg-gradient-to-r from-indigo-500/20 to-purple-500/20 rounded-xl blur-xl transform scale-105"></div>
        <canvas id="snakeCanvas" 
            class="relative mx-auto border-2 border-indigo-500/50 dark:border-indigo-400/50 rounded-xl bg-white/90 dark:bg-gray-800/90 shadow-xl max-w-full"
            width="400" height="400">
        </canvas>
    </div>
        
    <div class="mt-6 space-y-4 sm:space-y-0 sm:space-x-4">
        <button onclick="startGame()" 
            class="px-8 py-3 bg-gradient-to-r from-green-500 to-emerald-600 
                text-white rounded-xl shadow-lg 
                hover:shadow-xl hover:scale-[1.02] 
                active:scale-[0.98]
                transition-all duration-200
                font-semibold">
            <span class="flex items-center justify-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                </svg>
                Start Game
            </span>
        </button>
        <button hx-post="/includes/login.php" 
            hx-target="#login-form-container"
            class="px-8 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 
                text-white rounded-xl shadow-lg 
                hover:shadow-xl hover:scale-[1.02] 
                active:scale-[0.98]
                transition-all duration-200
                font-semibold">
            <span class="flex items-center justify-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                Back
            </span>
        </button>
    </div>

    <div class="mt-6 text-sm text-gray-500 dark:text-gray-400">
        Use arrow keys to control the snake, or swipe on mobile devices
    </div>
</div>

<script>
let canvas, ctx;
let snake = [];
let food = {};
let direction = 'right';
let gameLoop = null;
let score = 0;
let highScore = 0;
const gridSize = 20;
const gameSpeed = 150;

// Create custom popup element
const popup = document.createElement('div');
popup.className = 'fixed inset-0 flex items-center justify-center z-50 invisible opacity-0 transition-all duration-500';
popup.innerHTML = `
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm transition-all duration-500 opacity-0"></div>
    <div class="relative bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-xl max-w-sm mx-4 transform scale-95 transition-all duration-300">
        <div class="text-center">
            <h3 class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 dark:from-indigo-400 dark:to-purple-400 bg-clip-text text-transparent mb-2">Game Over!</h3>
            <p class="text-gray-600 dark:text-gray-400 mb-2">Score: <span id="finalScore" class="font-bold text-indigo-600 dark:text-indigo-400"></span></p>
            <p class="text-gray-600 dark:text-gray-400 mb-6">High Score: <span id="finalHighScore" class="font-bold text-purple-600 dark:text-purple-400"></span></p>
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <button onclick="closePopup(true)" 
                    class="px-6 py-2 bg-gradient-to-r from-green-500 to-emerald-600 
                        text-white rounded-xl shadow-lg 
                        hover:shadow-xl hover:scale-[1.02] 
                        active:scale-[0.98]
                        transition-all duration-200
                        font-semibold">
                    <span class="flex items-center justify-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                        </svg>
                        Play Again
                    </span>
                </button>
                <button onclick="closePopup(false)" 
                    class="px-6 py-2 bg-gradient-to-r from-indigo-500 to-purple-600 
                        text-white rounded-xl shadow-lg 
                        hover:shadow-xl hover:scale-[1.02] 
                        active:scale-[0.98]
                        transition-all duration-200
                        font-semibold">
                    <span class="flex items-center justify-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                        </svg>
                        Back
                    </span>
                </button>
            </div>
        </div>
    </div>
`;
document.body.appendChild(popup);

function showPopup() {
    popup.classList.remove('invisible', 'opacity-0');
    popup.querySelector('.absolute').classList.add('opacity-100');
    popup.querySelector('div:last-child').classList.remove('scale-95');
    popup.querySelector('div:last-child').classList.add('scale-100');
}

function closePopup(playAgain = true) {
    popup.classList.add('invisible', 'opacity-0');
    popup.querySelector('.absolute').classList.remove('opacity-100');
    popup.querySelector('div:last-child').classList.remove('scale-100');
    popup.querySelector('div:last-child').classList.add('scale-95');
    
    if (playAgain) {
        startGame();
    } else {
        // Go back to the main menu
        document.querySelector('button[hx-post="/includes/login.php"]').click();
    }
}

function initGame() {
    canvas = document.getElementById('snakeCanvas');
    if (!canvas) {
        console.error('Canvas element not found');
        return;
    }
    
    ctx = canvas.getContext('2d');
    if (!ctx) {
        console.error('Could not get canvas context');
        return;
    }
    
    // Adjust canvas size to fit container
    const container = canvas.parentElement;
    const size = Math.min(container.offsetWidth - 32, 400); // Max size of 400px, with padding
    canvas.width = size;
    canvas.height = size;
    
    // Initialize snake in the middle of the canvas
    const middleY = Math.floor((canvas.height / gridSize) / 2);
    snake = [
        {x: 3, y: middleY},
        {x: 2, y: middleY},
        {x: 1, y: middleY}
    ];
    
    // Load high score from localStorage
    highScore = parseInt(localStorage.getItem('snakeHighScore')) || 0;
    document.getElementById('highScore').textContent = highScore;
    
    // Create initial food
    createFood();
    
    // Initial draw
    draw();
    
    // Set up keyboard controls
    document.addEventListener('keydown', changeDirection);
    
    // Add touch controls for mobile
    setupTouchControls();
}

function setupTouchControls() {
    let touchStartX = 0;
    let touchStartY = 0;
    
    canvas.addEventListener('touchstart', function(e) {
        touchStartX = e.touches[0].clientX;
        touchStartY = e.touches[0].clientY;
        e.preventDefault();
    }, false);
    
    canvas.addEventListener('touchmove', function(e) {
        e.preventDefault();
    }, false);
    
    canvas.addEventListener('touchend', function(e) {
        let touchEndX = e.changedTouches[0].clientX;
        let touchEndY = e.changedTouches[0].clientY;
        
        let dx = touchEndX - touchStartX;
        let dy = touchEndY - touchStartY;
        
        // Determine swipe direction with minimum threshold
        const minSwipeDistance = 30;
        if (Math.abs(dx) > Math.abs(dy) && Math.abs(dx) > minSwipeDistance) {
            if (dx > 0 && direction !== 'left') direction = 'right';
            else if (dx < 0 && direction !== 'right') direction = 'left';
        } else if (Math.abs(dy) > minSwipeDistance) {
            if (dy > 0 && direction !== 'up') direction = 'down';
            else if (dy < 0 && direction !== 'down') direction = 'up';
        }
        
        e.preventDefault();
    }, false);
}

// Wait for the DOM to be fully loaded before initializing
let gameInitialized = false;

function ensureGameInitialized() {
    if (!gameInitialized) {
        initGame();
        gameInitialized = true;
    }
}

function startGame() {
    ensureGameInitialized();
    
    if (!canvas || !ctx) {
        console.error('Game not properly initialized');
        return;
    }
    
    if (gameLoop) {
        clearInterval(gameLoop);
    }
    
    // Reset game state
    const middleY = Math.floor((canvas.height / gridSize) / 2);
    snake = [
        {x: 3, y: middleY},
        {x: 2, y: middleY},
        {x: 1, y: middleY}
    ];
    direction = 'right';
    score = 0;
    document.getElementById('score').textContent = score;
    createFood();
    
    // Start game loop
    gameLoop = setInterval(gameStep, gameSpeed);
}

function createFood() {
    if (!canvas) return; // Guard clause
    
    food = {
        x: Math.floor(Math.random() * (canvas.width / gridSize)),
        y: Math.floor(Math.random() * (canvas.height / gridSize))
    };
    
    // Make sure food doesn't appear on snake
    while (snake.some(segment => segment.x === food.x && segment.y === food.y)) {
        food = {
            x: Math.floor(Math.random() * (canvas.width / gridSize)),
            y: Math.floor(Math.random() * (canvas.height / gridSize))
        };
    }
}

function changeDirection(event) {
    const key = event.key;
    
    if (key === 'ArrowUp' && direction !== 'down') direction = 'up';
    if (key === 'ArrowDown' && direction !== 'up') direction = 'down';
    if (key === 'ArrowLeft' && direction !== 'right') direction = 'left';
    if (key === 'ArrowRight' && direction !== 'left') direction = 'right';
}

// Enhanced particle system with smoother animations
class ParticleSystem {
    constructor() {
        this.particles = [];
    }

    createParticles(x, y, color) {
        const particleCount = 16;  // More particles for a fuller effect
        
        for (let i = 0; i < particleCount; i++) {
            const angle = (i / particleCount) * Math.PI * 2;
            const speed = 8 + Math.random() * 4; // More consistent speed
            
            this.particles.push({
                x: x,
                y: y,
                vx: Math.cos(angle) * speed,
                vy: Math.sin(angle) * speed,
                size: 3,  // Consistent size for smoother look
                alpha: 1,
                color: color
            });
        }
    }

    update() {
        for (let i = this.particles.length - 1; i >= 0; i--) {
            const p = this.particles[i];
            
            // Smoother movement
            p.x += p.vx;
            p.y += p.vy;
            
            // Gentle deceleration
            p.vx *= 0.98;
            p.vy *= 0.98;
            
            // Smoother fade out
            p.alpha *= 0.95;
            
            if (p.alpha < 0.01) {
                this.particles.splice(i, 1);
            }
        }
    }

    draw(ctx) {
        ctx.save();
        
        for (const p of this.particles) {
            ctx.globalAlpha = p.alpha;
            ctx.fillStyle = p.color;
            ctx.shadowColor = p.color;
            ctx.shadowBlur = 8;
            
            // Draw circular particles for smoother look
            ctx.beginPath();
            ctx.arc(p.x, p.y, p.size, 0, Math.PI * 2);
            ctx.fill();
        }
        
        ctx.restore();
    }
}

// Create particle system instance
const particleSystem = new ParticleSystem();

// Add snake segment animation
let newSegmentScale = 1;

function gameStep() {
    // Update particle system
    particleSystem.update();
    
    // Create new head based on direction
    const head = {x: snake[0].x, y: snake[0].y};
    
    switch(direction) {
        case 'up': head.y--; break;
        case 'down': head.y++; break;
        case 'left': head.x--; break;
        case 'right': head.x++; break;
    }
    
    // Check for collisions
    if (head.x < 0 || head.x >= canvas.width / gridSize ||
        head.y < 0 || head.y >= canvas.height / gridSize ||
        snake.some(segment => segment.x === head.x && segment.y === head.y)) {
        clearInterval(gameLoop);
        gameLoop = null;
        
        // Update high score
        if (score > highScore) {
            highScore = score;
            localStorage.setItem('snakeHighScore', highScore);
            document.getElementById('highScore').textContent = highScore;
        }
        
        // Update and show custom popup
        document.getElementById('finalScore').textContent = score;
        document.getElementById('finalHighScore').textContent = highScore;
        setTimeout(() => showPopup(), 100);
        return;
    }
    
    // Add new head
    snake.unshift(head);
    
    // Check if food is eaten
    if (head.x === food.x && head.y === food.y) {
        // Create particles with food color
        particleSystem.createParticles(
            (food.x + 0.5) * gridSize,
            (food.y + 0.5) * gridSize,
            'rgb(239, 68, 68)'
        );
        
        // More dynamic scale animation
        newSegmentScale = 1.8;  // Increased initial scale
        
        score += 10;
        document.getElementById('score').textContent = score;
        createFood();
    } else {
        snake.pop();
    }
    
    // Update new segment animation
    if (newSegmentScale > 1) {
        newSegmentScale = Math.max(1, newSegmentScale * 0.85);  // Smoother scale down
    }
    
    // Draw everything
    draw();
}

function draw() {
    // Clear canvas
    ctx.fillStyle = getComputedStyle(canvas).backgroundColor;
    ctx.fillRect(0, 0, canvas.width, canvas.height);
    
    // Draw grid (subtle)
    ctx.strokeStyle = 'rgba(99, 102, 241, 0.1)';
    ctx.lineWidth = 0.5;
    for (let i = 0; i <= canvas.width; i += gridSize) {
        ctx.beginPath();
        ctx.moveTo(i, 0);
        ctx.lineTo(i, canvas.height);
        ctx.stroke();
    }
    for (let i = 0; i <= canvas.height; i += gridSize) {
        ctx.beginPath();
        ctx.moveTo(0, i);
        ctx.lineTo(canvas.width, i);
        ctx.stroke();
    }
    
    // Draw snake with gradient effect
    snake.forEach((segment, index) => {
        const gradient = ctx.createLinearGradient(
            segment.x * gridSize,
            segment.y * gridSize,
            (segment.x + 1) * gridSize,
            (segment.y + 1) * gridSize
        );
        
        if (index === 0) {
            // Head colors
            gradient.addColorStop(0, 'rgb(99, 102, 241)');
            gradient.addColorStop(1, 'rgb(168, 85, 247)');
        } else {
            // Body colors
            gradient.addColorStop(0, 'rgb(129, 140, 248)');
            gradient.addColorStop(1, 'rgb(192, 132, 252)');
        }
        
        ctx.fillStyle = gradient;
        ctx.shadowColor = 'rgba(99, 102, 241, 0.5)';
        ctx.shadowBlur = 10;
        
        // Apply scale animation to new segments
        if (index === snake.length - 1 && newSegmentScale > 1) {
            const scale = newSegmentScale;
            const centerX = (segment.x + 0.5) * gridSize;
            const centerY = (segment.y + 0.5) * gridSize;
            
            ctx.save();
            ctx.translate(centerX, centerY);
            ctx.scale(scale, scale);
            ctx.translate(-centerX, -centerY);
        }
        
        ctx.beginPath();
        ctx.roundRect(
            segment.x * gridSize + 1,
            segment.y * gridSize + 1,
            gridSize - 2,
            gridSize - 2,
            4
        );
        ctx.fill();
        
        if (index === snake.length - 1 && newSegmentScale > 1) {
            ctx.restore();
        }
    });
    
    // Draw food with glow effect
    ctx.shadowColor = 'rgba(239, 68, 68, 0.5)';
    ctx.shadowBlur = 15;
    const foodGradient = ctx.createRadialGradient(
        (food.x + 0.5) * gridSize,
        (food.y + 0.5) * gridSize,
        2,
        (food.x + 0.5) * gridSize,
        (food.y + 0.5) * gridSize,
        gridSize / 2
    );
    foodGradient.addColorStop(0, 'rgb(239, 68, 68)');
    foodGradient.addColorStop(1, 'rgb(248, 113, 113)');
    ctx.fillStyle = foodGradient;
    ctx.beginPath();
    ctx.arc(
        (food.x + 0.5) * gridSize,
        (food.y + 0.5) * gridSize,
        gridSize / 2 - 1,
        0,
        Math.PI * 2
    );
    ctx.fill();
    
    // Draw particles
    particleSystem.draw(ctx);
    
    // Reset shadow
    ctx.shadowBlur = 0;
}

// Initialize game when loaded and when the element becomes available
document.addEventListener('DOMContentLoaded', function() {
    // Try immediate initialization
    initGame();
    
    // If initialization failed, retry with MutationObserver
    if (!canvas || !ctx) {
        const observer = new MutationObserver(function(mutations, obs) {
            const canvasElement = document.getElementById('snakeCanvas');
            if (canvasElement) {
                initGame();
                obs.disconnect(); // Stop observing once we find the canvas
            }
        });
        
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }
});

// Handle window resize
window.addEventListener('resize', function() {
    if (canvas) {
        const container = canvas.parentElement;
        const size = Math.min(container.offsetWidth - 32, 400);
        canvas.width = size;
        canvas.height = size;
        draw(); // Redraw the game
    }
});
</script> 