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

# Copy OPcache configuration
COPY php/conf.d/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# Configure PHP-FPM for better performance
RUN { \
    echo '[www]'; \
    echo 'pm = dynamic'; \
    echo 'pm.max_children = 50'; \
    echo 'pm.start_servers = 5'; \
    echo 'pm.min_spare_servers = 5'; \
    echo 'pm.max_spare_servers = 35'; \
    echo 'pm.max_requests = 500'; \
    echo 'request_terminate_timeout = 60s'; \
    echo 'catch_workers_output = yes'; \
} > /usr/local/etc/php-fpm.d/zz-docker.conf

# Create directory structure and set permissions during build
COPY --chown=www-data:www-data . /var/www/brute-force-demo/

# Create a user with the same UID/GID as your host user
RUN groupmod -g $GROUP_ID www-data \
    && usermod -u $USER_ID www-data

# Copy Nginx configuration
COPY nginx.conf /etc/nginx/nginx.conf
COPY nginx-site.conf /etc/nginx/conf.d/default.conf
RUN rm /etc/nginx/sites-enabled/default

# Create required directories with correct permissions
RUN mkdir -p /var/run/nginx /var/run/php-fpm /var/log/nginx \
    && chown -R www-data:www-data /var/run/nginx /var/run/php-fpm /var/log/nginx

# Copy and set permissions for entrypoint script
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 80

ENTRYPOINT ["docker-entrypoint.sh"]
