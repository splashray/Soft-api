FROM php:8.2-fpm

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        unzip \
        libzip-dev \
        libicu-dev \
        zlib1g-dev \
        libonig-dev \
        libsqlite3-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-install \
        pdo_mysql \
        pdo_sqlite \
        intl \
        mbstring \
        bcmath \
        zip \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

CMD ["php-fpm"]


