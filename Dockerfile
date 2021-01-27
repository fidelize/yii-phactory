FROM php:7.4-fpm-buster

RUN apt-get update && \
    apt-get upgrade -y && \
    apt-get install -y git

COPY --from=composer /usr/bin/composer /usr/bin/composer
