#!/bin/sh

# Ensure storage framework and log directories exist and are writable
mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache storage/logs
chmod -R 777 storage bootstrap/cache

# Copy env example to .env
cp .env.example .env

# Inject system environment variables into .env file
php -r "
\$keys = ['DB_CONNECTION', 'DB_HOST', 'DB_PORT', 'DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD', 'APP_KEY', 'APP_URL', 'APP_ENV', 'APP_DEBUG'];
\$content = file_get_contents('.env');
foreach (\$keys as \$k) {
    \$v = getenv(\$k);
    if (\$v !== false) {
        // Handle values with special characters
        \$content = preg_replace('/^' . \$k . '=.*/m', \$k . '=' . \$v, \$content);
    }
}
file_put_contents('.env', \$content);
"

# Run migrations
php artisan migrate --force

# Start the Laravel application server
php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
