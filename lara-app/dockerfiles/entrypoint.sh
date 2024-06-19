#!/bin/bash

# Check if the container is running as the PHP or queue service
if [[ "$HOSTNAME" == "php" ]]; then
    if [[ "$RUN_SETUP_COMMANDS" == "true" ]]; then
        # Run the setup commands
        php artisan key:generate
        php artisan passport:install
        php artisan db:seed
    fi
    
    if [[ "$DEPLOY_MODE" == "false" ]]; then
        # The DEPLOY_MODE env is set to false
        if [[ ! -f "vendor/autoload.php" ]]; then
            composer install --no-progress --no-interaction
        fi
        
        # Clear and optimize the application cache
        # php artisan storage:link --force
        php artisan optimize:clear
        php artisan optimize
        
        # Force database migrations
        php artisan migrate --force
    fi
    
    COMMAND="php artisan serve --port=$PORT --host=0.0.0.0 --env=.env"
    
    elif [[ "$HOSTNAME" == "queue" ]]; then
    COMMAND="/usr/bin/supervisord -n -c dockerfiles/queues/supervisord.conf"
else
    echo "[ ENTRYPOINT ] 'HOSTNAME' env is not recognized"
    exit 1
fi

# Execute the appropriate command
eval "$COMMAND"
exec docker-php-entrypoint "$@"
