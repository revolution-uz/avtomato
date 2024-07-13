#!/bin/sh
set -e

echo "🚚 Deploying application"

echo "📦 Installing composer dependencies"
su - www-data -s /bin/bash -c 'cd /var/www/automato && composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev'

echo "🗃️ Running migrations"
su - www-data -s /bin/bash -c 'cd /var/www/automato && php artisan migrate --force'
su - www-data -s /bin/bash -c 'cd /var/www/automato && php artisan tenants:migrate --force'

echo "🗃️ Running seeders"
su - www-data -s /bin/bash -c 'cd /var/www/automato && php artisan db:seed --force'
su - www-data -s /bin/bash -c 'cd /var/www/automato && php artisan tenants:seed --force'

echo "🧹 Clear cache"
su - www-data -s /bin/bash -c 'cd /var/www/automato && php artisan optimize:clear'

echo "🔄 Restarting Supervisor"
sudo supervisorctl restart all

echo "🎉 Deployed application"