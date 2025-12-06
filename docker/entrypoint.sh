#!/bin/bash

echo "==================================="
echo "  Sikades Lite - Container Startup"
echo "==================================="

cd /var/www/html

# Check if vendor folder exists
if [ ! -d "vendor" ]; then
    echo "📦 Installing Composer dependencies..."
    composer install --no-dev --optimize-autoloader --no-interaction
else
    echo "✅ Vendor folder exists"
fi

# Ensure writable directories exist with correct permissions
echo "📁 Setting up writable directories..."
mkdir -p /var/www/html/writable/logs
mkdir -p /var/www/html/writable/cache
mkdir -p /var/www/html/writable/session
mkdir -p /var/www/html/writable/uploads
chown -R www-data:www-data /var/www/html/writable
chmod -R 775 /var/www/html/writable

# Run database migrations (optional - uncomment if needed)
# echo "🔄 Running database migrations..."
# php spark migrate --all

echo "🚀 Starting Apache..."
exec apache2-foreground
