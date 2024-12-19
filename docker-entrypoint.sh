#!/bin/bash
set -e

# Set proper ownership for all files
chown -R www-data:www-data /var/www/brute-force-demo

# Set directory permissions
find /var/www/brute-force-demo -type d -exec chmod 755 {} \;

# Set file permissions
find /var/www/brute-force-demo -type f -exec chmod 644 {} \;

# Give execute permissions to PHP files
find /var/www/brute-force-demo -name "*.php" -exec chmod 755 {} \;

# Protect sensitive files
chmod 640 /var/www/brute-force-demo/data/users.txt

# Start PHP-FPM
php-fpm -D

# Start Nginx
nginx -g 'daemon off;' 