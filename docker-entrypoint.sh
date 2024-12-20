#!/bin/bash
set -e

# Create runtime directories with correct permissions
mkdir -p /var/run/nginx /var/run/php-fpm /var/log/nginx
chown -R www-data:www-data /var/run/nginx /var/run/php-fpm /var/log/nginx

# Start PHP-FPM
php-fpm -D

# Start Nginx
nginx -g 'daemon off;' 