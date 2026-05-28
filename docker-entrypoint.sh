#!/bin/sh
mkdir -p /var/www/storage/logs \
         /var/www/storage/framework/sessions \
         /var/www/storage/framework/views \
         /var/www/storage/framework/cache/data \
         /var/www/storage/app/public/posters
chown -R www-data:www-data /var/www/storage
php /var/www/artisan storage:link --force 2>/dev/null || true
service nginx start
php-fpm