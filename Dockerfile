# Use the official PHP image with Apache
FROM php:8.2-apache

# Copy the application code to the container's web root
COPY src/ /var/www/html/

# Set permissions for Apache
RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Install necessary PHP extensions
RUN docker-php-ext-install mysqli