FROM hub.hamdocker.ir/library/php:8.2

RUN apt-get update -y
RUN apt-get install -y unzip libpq-dev libcurl4-gnutls-dev supervisor
RUN docker-php-ext-install pdo pdo_mysql bcmath
RUN pecl install redis
RUN docker-php-ext-enable redis

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Create a directory for Supervisor logs
RUN mkdir -p /var/log/supervisor

# Copy the Supervisor configuration file
COPY /dockerfiles/queues/supervisord.conf /etc/supervisor/supervisord.conf

# Set the working directory
WORKDIR /var/www

# Image metadata:
ARG CI_COMMIT_SHORT_SHA
ARG CI_COMMIT_MESSAGE
ARG CI_PIPELINE_CREATED_AT
ARG CI_PIPELINE_URL

# https://github.com/opencontainers/image-spec/blob/main/annotations.md#back-compatibility-with-label-schema
LABEL org.opencontainers.image.vendor="PayLibero"
LABEL org.opencontainers.image.revision=${CI_COMMIT_SHORT_SHA}
LABEL org.opencontainers.image.created=${CI_PIPELINE_CREATED_AT}
LABEL org.opencontainers.image.source=${CI_PIPELINE_URL}

# preserve build time ARGs at runtime too.
ENV CI_COMMIT_SHORT_SHA ${CI_COMMIT_SHORT_SHA}
ENV CI_PIPELINE_CREATED_AT ${CI_PIPELINE_CREATED_AT}
ENV CI_PIPELINE_URL ${CI_PIPELINE_URL}

# Copy the Laravel application code
COPY .. .
RUN composer install --no-interaction

ENV PORT=8000
EXPOSE 8000

# Run Supervisor
CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/supervisord.conf"]