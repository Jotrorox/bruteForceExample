# Use the official PHP-FPM image
FROM php:8.2-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    nginx \
    && rm -rf /var/lib/apt/lists/*

# Configure PHP
RUN docker-php-ext-install opcache pdo_mysql

# Create directory structure
RUN mkdir -p /var/www/brute-force-demo/{public,config,data,includes}

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
    && chmod -R 755 /var/www/brute-force-demo/public \
    && chmod -R 750 /var/www/brute-force-demo/includes \
    && chmod -R 750 /var/www/brute-force-demo/config \
    && chmod -R 750 /var/www/brute-force-demo/data \
    && chmod 640 /var/www/brute-force-demo/data/users.txt

# Create required directories
RUN mkdir -p /var/run/nginx /var/log/nginx \
    && chown -R www-data:www-data /var/run/nginx /var/log/nginx

# Start PHP-FPM and Nginx
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 80

ENTRYPOINT ["docker-entrypoint.sh"]
