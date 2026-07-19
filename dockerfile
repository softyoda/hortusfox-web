# Stage 1: Composer installation
FROM composer:latest AS composer

WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-scripts --no-autoloader
RUN composer dump-autoload --optimize

# Stage 2: Node / webpack build
FROM node:20-alpine AS node_builder

WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY webpack.config.js ./
COPY app/resources ./app/resources
RUN npm run build

# Stage 3: Apache + PHP runtime
FROM php:8.4-apache

WORKDIR /var/www/html

RUN apt-get update \
 && DEBIAN_FRONTEND=noninteractive apt-get install -y \
        libpng-dev \
        libjpeg-dev \
        libonig-dev \
        libxml2-dev \
        libzip-dev \
        libicu-dev \
        zip \
        unzip \
        git \
        default-mysql-client \
        tzdata \
 && apt-get clean \
 && rm -rf /var/lib/apt/lists/* \
 && docker-php-ext-install \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        intl \
        zip \
 && docker-php-ext-configure gd --with-jpeg \
 && docker-php-ext-install gd

RUN a2enmod rewrite

EXPOSE 80

# Copy application source
COPY . /var/www/html

# Overwrite pre-committed JS bundle with the freshly built one
COPY --from=node_builder /app/public/js/app.js /var/www/html/public/js/app.js

# Copy default files in /public/img so they can be copied if needed in entrypoint
RUN mkdir /tmp/img \
 && cp /var/www/html/public/img/* /tmp/img
VOLUME ["/var/www/html/public/img"]

VOLUME ["/var/www/html/app/logs"]
VOLUME ["/var/www/html/public/backup"]

RUN mkdir /tmp/themes \
 && cp -r /var/www/html/public/themes/* /tmp/themes
VOLUME ["/var/www/html/public/themes"]

RUN mkdir /tmp/migrations \
 && cp /var/www/html/app/migrations/* /tmp/migrations
VOLUME ["/var/www/html/app/migrations"]

COPY ./99-php.ini /usr/local/etc/php/conf.d/

COPY --from=composer /app/vendor/ /var/www/html/vendor/
COPY --from=composer /usr/bin/composer /usr/local/bin/composer

COPY --chmod=555 docker-entrypoint.sh /usr/local/bin/

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["apache2-foreground"]
