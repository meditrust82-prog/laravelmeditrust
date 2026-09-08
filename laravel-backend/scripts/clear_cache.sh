#!/usr/bin/env bash
set -e
php artisan route:clear
php artisan config:clear
php artisan view:clear
php artisan cache:clear
echo "Laravel caches cleared successfully."
