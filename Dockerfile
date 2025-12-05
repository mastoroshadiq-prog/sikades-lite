FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libicu-dev \
    && docker-php-ext-install intl mysqli pdo pdo_mysql mbstring exif pcntl bcmath gd \
    && a2enmod rewrite

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Configure Apache document root
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

# Update Apache configuration
RUN sed -i 's|/var/www/html|${APACHE_DOCUMENT_ROOT}|g' /etc/apache2/sites-available/000-default.conf \
    && sed -i 's|/var/www/|${APACHE_DOCUMENT_ROOT}|g' /etc/apache2/apache2.conf

# Set permissions for writable directory
RUN mkdir -p /var/www/html/writable && \
    chown -R www-data:www-data /var/www/html/writable && \
    chmod -R 775 /var/www/html/writable

# Expose port 80
EXPOSE 80

CMD ["apache2-foreground"]
