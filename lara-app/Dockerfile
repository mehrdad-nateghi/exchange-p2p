FROM php:8.2 as php

RUN apt-get update -y
RUN apt-get install -y unzip libpq-dev libcurl4-gnutls-dev
RUN docker-php-ext-install pdo pdo_mysql bcmath

WORKDIR /var/www

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY ./dockerfiles/entrypoint.sh /usr/local/bin/entrypoint.sh

ENV PORT=8000
ENV INITIAL_SETUP=false

RUN chmod +x /usr/local/bin/entrypoint.sh
ENTRYPOINT [ "/usr/local/bin/entrypoint.sh" ]

