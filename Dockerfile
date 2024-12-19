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
RUN mkdir -p /var/log/nginx /var/run/nginx && \
    chown -R www-data:www-data /var/log/nginx /var/run/nginx

# Update NGINX configuration to use localhost instead of php-fpm hostname
COPY nginx.conf /etc/nginx/conf.d/default.conf
RUN sed -i 's/php-fpm:9000/localhost:9000/g' /etc/nginx/conf.d/default.conf

# Copy supervisor configuration
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Expose port 80
EXPOSE 80

# Start supervisor
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
