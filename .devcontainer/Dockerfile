FROM mcr.microsoft.com/devcontainers/php:8.4-bookworm

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        default-mysql-client \
        libfreetype6-dev \
        libicu-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libwebp-dev \
        libxml2-dev \
        libxslt1-dev \
        libzip-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j"$(nproc)" \
        bcmath \
        ftp \
        gd \
        intl \
        mysqli \
        pdo_mysql \
        soap \
        sockets \
        xsl \
        zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*
