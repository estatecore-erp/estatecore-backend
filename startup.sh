#!/bin/bash
cp /home/site/wwwroot/deployment/azure-app-service/nginx.conf /etc/nginx/sites-available/default
service nginx reload

mkdir -p /home/site/wwwroot/storage/framework/views
mkdir -p /home/site/wwwroot/storage/framework/cache
mkdir -p /home/site/wwwroot/storage/framework/sessions
mkdir -p /home/site/wwwroot/storage/logs
chmod -R 775 /home/site/wwwroot/storage /home/site/wwwroot/bootstrap/cache

php artisan config:cache
php artisan migrate --force
php-fpm