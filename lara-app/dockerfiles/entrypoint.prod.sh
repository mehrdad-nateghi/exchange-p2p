#!/bin/bash

if [ ! -f "vendor/autoload.php" ]
then
    composer install --no-progress --no-interaction
fi


# php artisan storage:link --force
php artisan optimize:clear
php artisan optimize
php artisan migrate --force

COMMAND="php artisan serve --port=$PORT --host=0.0.0.0 --env=.env"

# Execute the appropriate command
eval "$COMMAND"
exec docker-php-entrypoint "$@"
