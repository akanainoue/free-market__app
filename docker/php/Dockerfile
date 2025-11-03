FROM php:7.4.9-fpm

COPY php.ini /usr/local/etc/php/

RUN sed -i 's|http://deb.debian.org/debian|http://archive.debian.org/debian|g' /etc/apt/sources.list \
    && sed -i 's|http://security.debian.org/debian-security|http://archive.debian.org/debian-security|g' /etc/apt/sources.list \
    && apt update && apt install -y \
        curl \
        unzip \
        default-mysql-client \
        zlib1g-dev \
        libzip-dev \
        gnupg \
        ca-certificates \
    && curl -fsSL https://deb.nodesource.com/setup_16.x | bash - \
    && apt install -y nodejs \
    && docker-php-ext-install pdo_mysql zip

    # apt update \
    # && apt install -y default-mysql-client zlib1g-dev libzip-dev unzip \
    # && docker-php-ext-install pdo_mysql zip

RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer \
    && composer self-update

WORKDIR /var/www
