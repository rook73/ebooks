FROM php:7.4-fpm AS build

RUN apt-get update \
    && apt-get install -y -qq --no-install-recommends \
        vim \
        libzip-dev \
        unzip \
        zlib1g-dev \
        libicu-dev \
        g++ \
    && rm -rf /var/lib/apt/lists/* \
    && apt-get clean

COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN docker-php-ext-install \
        intl \
        zip \
        pcntl \
        pdo_mysql \
        bcmath \
        opcache \
    && pecl install \
        apcu \
        igbinary \
    && docker-php-ext-enable \
        apcu \
        igbinary

RUN chown www-data:www-data /var/www && \
    mkdir -p ./var && chown -R www-data:www-data ./var

COPY .build/php/custom.ini /usr/local/etc/php/conf.d/custom.ini

RUN ln -snf /usr/share/zoneinfo/${TIMEZONE} /etc/localtime && \
    echo ${TIMEZONE} > /etc/timezone

WORKDIR /var/www

EXPOSE 9000

FROM build as develop
ENV APP_ENV dev
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && usermod -u 1000 -o www-data && groupmod -g 1000 -o www-data \
    && echo "opcache.validate_timestamps=1" >> /usr/local/etc/php/conf.d/custom.ini

COPY .build/php/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

USER 1000:1000

FROM build as release
ENV APP_ENV prod
COPY --chown=www-data:www-data . .
RUN composer install --prefer-dist --no-interaction --no-dev --optimize-autoloader
