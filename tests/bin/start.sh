#!/usr/bin/env bash

cd ./laravel-tests
export DISPLAY=:99.0
#sudo Xvfb :99.0 &
sudo chmod -R 0755 ./vendor/laravel/dusk/bin/
php artisan serve --port=8300 > /dev/null 2>&1 &
