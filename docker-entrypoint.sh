#!/bin/bash
set -e

# Ensure proper permissions
chown -R www-data:www-data /var/www/brute-force-demo
chmod -R 755 /var/www/brute-force-demo/public
chmod -R 750 /var/www/brute-force-demo/includes
chmod -R 750 /var/www/brute-force-demo/config
chmod -R 750 /var/www/brute-force-demo/data
chmod 640 /var/www/brute-force-demo/data/users.txt

# Start PHP-FPM
php-fpm -D

# Start Nginx
nginx -g 'daemon off;' 