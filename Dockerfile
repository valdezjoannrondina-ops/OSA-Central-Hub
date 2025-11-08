# syntax=docker/dockerfile:1

FROM dunglas/frankenphp:php8.2-bookworm

# Install system dependencies, Node.js 18, and Composer
RUN apt-get update && apt-get install -y curl gnupg unzip && \
    curl -fsSL https://deb.nodesource.com/setup_18.x | bash - && \
    apt-get install -y nodejs && \
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php composer-setup.php --install-dir=/usr/local/bin --filename=composer && \
    php -r "unlink('composer-setup.php');" && \
    rm -rf /var/lib/apt/lists/*

# Install required PHP extensions
RUN install-php-extensions ctype curl dom fileinfo filter hash mbstring \
    openssl pcre pdo_mysql pdo_pgsql session tokenizer xml gd

# Set working directory
WORKDIR /app

# Copy composer files, scripts, and install dependencies
COPY composer.json composer.lock ./
COPY scripts ./scripts
RUN composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader

# Copy package files and install node dependencies
COPY package.json package-lock.json ./
RUN npm ci

# Copy application source
COPY . .

# Build assets and run artisan optimizations
RUN npm run build && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache

# Expose port for Laravel's internal server
EXPOSE 8000

# Default command
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
