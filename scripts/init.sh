#!/bin/sh

set -e

echo 'Recreating database. Started.'
docker exec -t avtomato-db su -c "psql -c \"DROP DATABASE IF EXISTS avtomato WITH (FORCE);\"" postgres
docker exec -t avtomato-db su -c "psql -c \"DROP DATABASE IF EXISTS avtomato_test WITH (FORCE);\"" postgres
docker exec -t avtomato-db su -c "psql -c \"DROP DATABASE IF EXISTS company_1 WITH (FORCE);\"" postgres
docker exec -t avtomato-db su -c "psql -c \"CREATE DATABASE avtomato;\"" postgres
docker exec -t avtomato-db su -c "psql -c \"CREATE DATABASE avtomato_test;\"" postgres
echo 'Recreating database. Finished.'

echo 'Installing composer packages. Started.'
docker exec -t avtomato-app composer install
echo 'Installing composer packages. Finished.'

echo 'Migrating database. Started.'
docker exec -t avtomato-app php artisan migrate --seed --force
docker exec -t avtomato-app php artisan migrate --database=database_test --force
echo 'Migrating database. Finished.'

echo 'Seeding dummy data. Started.'
docker exec -t avtomato-app php artisan db:seed DummySeeder
echo 'Seeding dummy data. Finished.'

echo 'Clearing cache. Started.'
docker exec -t avtomato-app php artisan optimize:clear
echo 'Clearing cache. Finished.'
