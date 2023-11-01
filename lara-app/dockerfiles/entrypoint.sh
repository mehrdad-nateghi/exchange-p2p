#!/bin/bash

if [ ! -f "vendor/autoload.php" ]
then
    composer install --no-progress --no-interaction
fi

if [ "$RUN_SETUP_COMMANDS" = "true" ]
then
    # Run the setup commands
    php artisan key:generate
    php artisan migrate
    php artisan cache:clear
    php artisan config:clear
    php artisan rout:clear
    php artisan passport:install
    php artisan db:seed
fi



php artisan serve --port=$PORT --host=0.0.0.0 --env=.env
exec docker-php-entrypoint "$@"
