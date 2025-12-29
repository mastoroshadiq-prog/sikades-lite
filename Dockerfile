FROM php:8.2-apache

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    libicu-dev \
    libpng-dev \
    libonig-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-install \
    pdo \
    pdo_pgsql \
    pgsql \
    intl \
    zip \
    gd \
    mbstring \
    opcache \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Enable Apache modules
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy composer files first for caching
COPY composer.json ./
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader --no-interaction --ignore-platform-reqs

# Copy application code
COPY . .

# Set permissions for writable directory
RUN mkdir -p /tmp/writable/cache /tmp/writable/logs /tmp/writable/session /tmp/writable/uploads /tmp/writable/debugbar \
    && chmod -R 777 /tmp/writable

# Configure Apache DIRECTLY without .htaccess
RUN echo '<VirtualHost *:8080>\n\
    DocumentRoot /var/www/html/public\n\
    <Directory /var/www/html/public>\n\
    Options -Indexes +FollowSymLinks\n\
    AllowOverride None\n\
    Require all granted\n\
    \n\
    # Direct Apache config for CI4 routing (no .htaccess)\n\
    RewriteEngine On\n\
    RewriteCond %{REQUEST_FILENAME} !-f\n\
    RewriteCond %{REQUEST_FILENAME} !-d\n\
    RewriteRule ^(.*)$ index.php?/$1 [L,QSA]\n\
    </Directory>\n\
    ErrorLog ${APACHE_LOG_DIR}/error.log\n\
    CustomLog ${APACHE_LOG_DIR}/access.log combined\n\
    </VirtualHost>' > /etc/apache2/sites-available/000-default.conf

# Create startup script that handles PORT dynamically
RUN echo '#!/bin/bash\n\
    set -e\n\
    PORT=${PORT:-8080}\n\
    # Update port in Apache config\n\
    sed -i "s/:8080/:$PORT/" /etc/apache2/sites-available/000-default.conf\n\
    echo "Listen $PORT" > /etc/apache2/ports.conf\n\
    exec apache2-foreground' > /usr/local/bin/start-apache.sh \
    && chmod +x /usr/local/bin/start-apache.sh

EXPOSE 8080

CMD ["/usr/local/bin/start-apache.sh"]
