<?php
class Navigation
{
    private array $pages = [
        'index.php' => [
            'title' => 'Login Page',
            'description' => 'Main login interface',
            'icon' => '🔒'
        ],
        'bruteforce.php' => [
            'title' => 'Brute Force Testing',
            'description' => 'Educational brute force demonstration',
            'icon' => '🔨'
        ]
    ];

    private string $currentPage;

    public function __construct()
    {
        $this->currentPage = basename($_SERVER['PHP_SELF']);
    }

    public function render(): string
    {
        $html = '<nav class="site-navigation">';

        // Navigation links
        $html .= '<div class="nav-links">';
        foreach ($this->pages as $page => $info) {
            $isActive = ($this->currentPage === $page);
            $activeClass = $isActive ? 'active' : '';

            $html .= sprintf(
                '<a href="/%s" class="nav-item %s" title="%s">
                    <span class="nav-icon">%s</span>
                    <span class="nav-title">%s</span>
                </a>',
                htmlspecialchars($page),
                $activeClass,
                htmlspecialchars($info['description']),
                $info['icon'],
                htmlspecialchars($info['title'])
            );
        }
        $html .= '</div>';

        // Session status
        if (session_status() === PHP_SESSION_ACTIVE) {
            $attempts = $_SESSION['attempts'] ?? 0;
            if ($attempts > 0) {
                $html .= sprintf(
                    '<div class="nav-status">
                        <span class="attempt-counter">Attempts: %d</span>
                    </div>',
                    $attempts
                );
            }
        }

        $html .= '</nav>';

        // Add CSS
        $html .= $this->getStyles();

        return $html;
    }

    private function getStyles(): string
    {
        return '<style>
            .site-navigation {
                margin: 0 0 20px 0;
                font-family: Arial, sans-serif;
            }

            .nav-links {
                display: flex;
                gap: 10px;
                flex-wrap: wrap;
                background: white;
                padding: 10px;
                border-radius: 8px;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            }

            .nav-item {
                display: flex;
                align-items: center;
                gap: 8px;
                padding: 8px 16px;
                text-decoration: none;
                color: #333;
                border-radius: 4px;
                transition: all 0.2s ease;
            }

            .nav-item:hover {
                background: #f0f0f0;
            }

            .nav-item.active {
                background: #007bff;
                color: white;
            }

            .nav-icon {
                font-size: 1.2em;
            }

            .nav-status {
                margin-top: 10px;
                padding: 8px;
                background: #f8f9fa;
                border-radius: 4px;
                text-align: center;
                font-size: 0.9em;
                color: #666;
            }

            .attempt-counter {
                background: #fff3cd;
                color: #856404;
                padding: 4px 8px;
                border-radius: 4px;
                font-weight: bold;
            }
        </style>';
    }
}