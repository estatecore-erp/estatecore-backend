#!/bin/bash
cp /home/site/wwwroot/deployment/azure-app-service/nginx.conf /etc/nginx/sites-available/default
service nginx reload
php artisan config:cache
php artisan migrate --force
php-fpm