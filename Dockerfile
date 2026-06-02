FROM php:8.2-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring zip exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install Node.js & NPM (needed to compile Vite assets)
RUN curl -sL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y nodejs

# Set working directory
WORKDIR /app

# Copy project files
COPY . .

# Copy env example to env if .env is ignored in git
RUN cp .env.example .env

# Install Composer dependencies
RUN composer install --no-dev --optimize-autoloader

# Install NPM dependencies and build assets
RUN npm install && npm run build

# Expose port
EXPOSE 8000

# Set entrypoint command to run migrations and start server
CMD php artisan key:generate && php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
