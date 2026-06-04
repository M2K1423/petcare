#!/bin/sh

# Ensure storage framework and log directories exist and are writable
mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache storage/logs
chmod -R 777 storage bootstrap/cache

# Copy env example to .env
cp .env.example .env

# Inject system environment variables into .env file
php -r "
\$keys = [
    'DB_CONNECTION', 'DB_HOST', 'DB_PORT', 'DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD', 
    'APP_KEY', 'APP_URL', 'APP_ENV', 'APP_DEBUG',
    'VNPAY_ENABLED', 'VNPAY_TMN_CODE', 'VNPAY_HASH_SECRET', 'VNPAY_PAYMENT_URL', 
    'VNPAY_RETURN_URL', 'VNPAY_IPN_URL', 'VNPAY_LOCALE', 'VNPAY_ORDER_TYPE', 'VNPAY_EXPIRE_MINUTES',
    'REVERB_APP_ID', 'REVERB_APP_KEY', 'REVERB_APP_SECRET', 'REVERB_HOST', 'REVERB_PORT', 'REVERB_SCHEME',
    'VITE_REVERB_APP_KEY', 'VITE_REVERB_HOST', 'VITE_REVERB_PORT', 'VITE_REVERB_SCHEME',
    'BROADCAST_CONNECTION', 'PUSHER_APP_ID', 'PUSHER_APP_KEY', 'PUSHER_APP_SECRET', 'PUSHER_APP_CLUSTER',
    'VITE_BROADCAST_CONNECTION', 'VITE_PUSHER_APP_KEY', 'VITE_PUSHER_APP_CLUSTER',
    'AI_SERVICE_URL', 'AI_SERVICE_API_KEY', 'QUEUE_CONNECTION'
];
\$content = file_get_contents('.env') . \"\n\";
foreach (\$keys as \$k) {
    \$v = getenv(\$k);
    if (\$v !== false) {
        \$content .= \$k . '=' . \$v . \"\n\";
    }
}
file_put_contents('.env', \$content);
"

# Run migrations
php artisan migrate --force

# Start the Laravel application server
php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
