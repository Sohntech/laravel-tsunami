#!/bin/bash
# Démarre PHP-FPM
php-fpm &

# Démarre Nginx
nginx -g "daemon off;"
