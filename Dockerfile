FROM php:8.1-fpm

# Instal ekstensi PHP dan dependensi sistem yang diperlukan
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    mariadb-client \
    curl \
    git \
    supervisor \
    && rm -rf /var/lib/apt/lists/*

# Instal driver pdo_mysql dan ekstensi lainnya
RUN docker-php-ext-install pdo_mysql exif pcntl gd
RUN docker-php-ext-configure gd --with-jpeg --with-freetype && docker-php-ext-install -j$(nproc) gd

# Instal Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

CMD ["php-fpm"]
