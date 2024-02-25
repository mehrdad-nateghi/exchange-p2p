FROM php:8.2
# as php

RUN apt-get update -y
RUN apt-get install -y unzip libpq-dev libcurl4-gnutls-dev
RUN docker-php-ext-install pdo pdo_mysql bcmath

WORKDIR /var/www

ARG CI_COMMIT_SHORT_SHA
ARG CI_COMMIT_MESSAGE
ARG CI_PIPELINE_CREATED_AT

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
# COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY ./dockerfiles/entrypoint.sh /usr/local/bin/entrypoint.sh

# Image metadata:
ARG CI_COMMIT_SHORT_SHA
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


ENV PORT=8000

EXPOSE 8000

RUN chmod +x /usr/local/bin/entrypoint.sh
ENTRYPOINT [ "/usr/local/bin/entrypoint.sh" ]

