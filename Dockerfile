# Use the official PHP-FPM image
FROM php:8.2-fpm

# Install NGINX and supervisor with cleanup in the same layer
RUN apt-get update && \
    apt-get install -y \
    nginx \
    supervisor \
    && rm -rf /var/lib/apt/lists/*

# Copy application code
COPY src/ /var/www/html/

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Create required directories and set permissions
RUN mkdir -p /var/log/nginx /var/run/nginx /var/log/supervisor && \
    chown -R www-data:www-data /var/log/nginx /var/run/nginx

# Copy configuration files
COPY nginx.conf /etc/nginx/conf.d/default.conf
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Expose ports
EXPOSE 80 9000

# Start supervisor
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
