#!/bin/sh

cd "$(dirname "$0")" || exit

mkdir storage/framework/sessions;

echo "Applying correct chmod settings"
find storage -type d -exec chmod 775 {} \;
find storage -type f -exec chmod 664 {} \;
find bootstrap/cache -type d -exec chmod 775 {} \;
find bootstrap/cache -type f -exec chmod 664 {} \;

echo "Running migrations..."
php artisan migrate --force

echo "Restarting queues..."
php artisan queue:restart
