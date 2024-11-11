# First, the Dockerfile
FROM php:8.2-apache

# Install PHP extensions and enable Apache modules
RUN docker-php-ext-install session && \
    a2enmod rewrite

# Set up Apache configuration
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Set working directory
WORKDIR /var/www/html

# Copy source files
COPY src/ /var/www/html/

# Set permissions
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html