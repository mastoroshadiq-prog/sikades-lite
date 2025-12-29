#!/bin/bash
set -e

# Get PORT from environment or default to 8080
PORT="${PORT:-8080}"

echo "Configuring Apache to listen on port $PORT..."

# Update Apache configuration with actual PORT value
sed -i "s/Listen 8080/Listen $PORT/" /etc/apache2/ports.conf
sed -i "s/:8080/:$PORT/" /etc/apache2/sites-available/000-default.conf

echo "Starting Apache on port $PORT..."
exec apache2-foreground
