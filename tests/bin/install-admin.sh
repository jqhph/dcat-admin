#!/usr/bin/env bash

cd ./laravel-tests
php artisan admin:publish --force
php artisan admin:install
php artisan migrate:rollback
php artisan dusk:chrome-driver 102
cp -f ./tests/routes.php ./app/Admin/
cp -rf ./tests/resources/config ./config/
