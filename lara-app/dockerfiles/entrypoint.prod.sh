#!/bin/bash

if [ ! -f "vendor/autoload.php" ]
then
    composer install --no-progress --no-interaction
fi

COMMAND="php artisan serve --port=$PORT --host=0.0.0.0 --env=.env"

# Execute the appropriate command
eval "$COMMAND"
exec docker-php-entrypoint "$@"
