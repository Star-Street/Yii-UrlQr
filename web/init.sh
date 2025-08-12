#!/bin/bash
set -e

cd /var/www/html || exit 1

echo "### ######################"
echo "### Installing composer dependencies..."
if [ "$APP_ENV" = "prod" ]; then
    composer install --no-dev --optimize-autoloader --no-interaction --no-progress
else
    composer install --optimize-autoloader --no-interaction --no-progress
fi

echo "### ######################"
echo "### Setting permissions..."
find . -type d -exec chmod 755 {} \;
find . -type f -exec chmod 644 {} \;
chown -R www-data:www-data .
chmod +x yii

echo "### ######################"
echo "### Applying migrations..."
php yii migrate --interactive=0

echo "### End initial..."
echo "### ######################"