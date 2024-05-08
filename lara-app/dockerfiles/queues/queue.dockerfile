FROM hub.hamdocker.ir/library/php:8.2

# Install required PHP extensions
RUN docker-php-ext-install pdo pdo_mysql

# Install Supervisor
RUN apt-get update && apt-get install -y supervisor

# Create a directory for Supervisor logs
RUN mkdir -p /var/log/supervisor

# Copy the Supervisor configuration file
COPY ./dockerfiles/queues/supervisord.conf /etc/supervisor/supervisord.conf

# Set the working directory
WORKDIR /var/www

# Copy the Laravel application code
COPY .. .

# Run Supervisor
CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/supervisord.conf"]