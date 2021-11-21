FROM php:7.4-fpm as soft

WORKDIR /app

RUN apt update \
    && apt install -y libxml2-dev \
        libpng-dev \
        libjpeg-dev \
        libfreetype6-dev \
    && apt clean

RUN docker-php-ext-install \
        ctype \
        iconv \
        json \
        soap \
        pdo \
        pdo_mysql

COPY --from=composer /usr/bin/composer /usr/bin/composer


################

FROM soft as builder
COPY --from=composer /usr/bin/composer /usr/bin/composer
COPY . .
RUN apt update \
    && apt install -y zip unzip git libzip-dev \
    && apt clean \
    && docker-php-ext-install zip

################

FROM soft as app
COPY --from=source /app .
RUN echo "APP_ENV=prod" >> /app/.env.local
