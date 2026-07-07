FROM php:8.2-cli

ARG NODE_VERSION=20

# Install PHP extensions and system packages
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    unzip \
    libicu-dev \
    libonig-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libpq-dev \
    libsqlite3-dev \
    curl \
    gnupg \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo \
        pdo_mysql \
        pdo_pgsql \
        pdo_sqlite \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        intl \
    && rm -rf /var/lib/apt/lists/*


# Install Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_${NODE_VERSION}.x | bash - \
    && apt-get install -y nodejs \
    && npm install -g npm@latest


# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer


WORKDIR /var/www/html


# Copy Laravel project
COPY . .


# Install PHP dependencies
RUN composer install \
    --no-interaction \
    --no-progress \
    --prefer-dist \
    --no-dev \
    --optimize-autoloader


# Install frontend dependencies
RUN npm install


# Permissions only (do not run artisan commands here)
RUN chmod -R 775 storage bootstrap/cache


# Build frontend assets
RUN npm run build


ENV PORT=10000

EXPOSE 10000


# Run Laravel after Render provides environment variables
CMD ["sh", "-c", "php artisan migrate --force && php artisan storage:link && php artisan config:cache && php artisan route:cache && php artisan view:cache && php artisan serve --host 0.0.0.0 --port ${PORT}"]