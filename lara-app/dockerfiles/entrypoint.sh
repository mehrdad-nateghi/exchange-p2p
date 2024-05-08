#!/bin/bash

# Check if the container is running as the PHP or queue service
if [[ "$HOSTNAME" == "php" ]]; then
    if [ ! -f "vendor/autoload.php" ]
    then
        composer install --no-progress --no-interaction
    fi

    if [ "$RUN_SETUP_COMMANDS" = "true" ]
    then
        # Run the setup commands
        php artisan key:generate
        php artisan passport:install
        php artisan db:seed
    fi

    # php artisan storage:link --force
    php artisan optimize:clear
    php artisan optimize
    php artisan migrate --force

    COMMAND="php artisan serve --port=$PORT --host=0.0.0.0 --env=.env"

elif [[ "$HOSTNAME" == "queue" ]]; then
    COMMAND="php artisan queue:work --tries=3 --backoff=5"
else
    echo "Invalid container type"
    exit 1
fi

# Execute the appropriate command
eval "$COMMAND"
exec docker-php-entrypoint "$@"
