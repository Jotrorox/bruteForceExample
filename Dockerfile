# Use the official PHP-FPM image
FROM php:8.2-fpm

# Copy the application code to the container
COPY src/ /var/www/html/

# Set permissions for NGINX
RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html
