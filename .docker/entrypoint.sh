#!/usr/bin/env bash

service nginx start

service php8.2-fpm start

chmod a+rw -R .

composer install --working-dir=/var/www/html/api

npm install --prefix /var/www/html/api

# php artisan migrate:fresh --seed

echo ""
echo ""
echo "========================================"
echo ""
echo "CONTAINER STARTED SUCCESSFULLY!"
echo ""
echo "Press Ctrl + C to exit from logs"
echo ""
echo "========================================"
echo ""
echo ""

# npm run watch

# prevent container from exiting
/bin/bash
