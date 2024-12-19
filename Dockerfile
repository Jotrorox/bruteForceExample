# Use the official PHP-FPM image
FROM php:8.2-fpm

# Accept build arguments for user/group IDs
ARG USER_ID=1000
ARG GROUP_ID=1000

# Install dependencies
RUN apt-get update && apt-get install -y \
    nginx \
    && rm -rf /var/lib/apt/lists/*

# Configure PHP
RUN docker-php-ext-install opcache pdo_mysql

# Create directory structure
RUN mkdir -p /var/www/brute-force-demo/{public,config,data,includes}

# Create a user with the same UID/GID as your host user
RUN groupmod -g $GROUP_ID www-data \
    && usermod -u $USER_ID www-data

# Copy application files
COPY public/ /var/www/brute-force-demo/public/
COPY config/ /var/www/brute-force-demo/config/
COPY includes/ /var/www/brute-force-demo/includes/
COPY data/ /var/www/brute-force-demo/data/

# Copy Nginx configuration
COPY nginx-site.conf /etc/nginx/conf.d/default.conf
RUN rm /etc/nginx/sites-enabled/default

# Set permissions
RUN chown -R www-data:www-data /var/www/brute-force-demo \
    && find /var/www/brute-force-demo -type d -exec chmod 755 {} \; \
    && find /var/www/brute-force-demo -type f -exec chmod 644 {} \; \
    && find /var/www/brute-force-demo -name "*.php" -exec chmod 755 {} \; \
    && chmod 640 /var/www/brute-force-demo/data/users.txt

# Create required directories
RUN mkdir -p /var/run/nginx /var/log/nginx \
    && chown -R www-data:www-data /var/run/nginx /var/log/nginx

# Start PHP-FPM and Nginx
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 80

ENTRYPOINT ["docker-entrypoint.sh"]
