FROM php:8.3-apache-bookworm as app-local

ENV APP_ENV=local
ENV APP_DEBUG=true

# Install dependencies

RUN apt-get update && apt-get install -y \
    zlib1g-dev zip \
    libjpeg-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libpng-dev \
    supervisor \
    default-mysql-client \
    curl \
    && curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y nodejs


RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install -j$(nproc) gd

RUN apt-get install -y jpegoptim optipng pngquant gifsicle libzip-dev libonig-dev

RUN docker-php-ext-install bcmath && docker-php-ext-enable bcmath

RUN docker-php-ext-install sockets

# Install PHP SQL Server connection
RUN apt-get install -y freetds-dev \
    && docker-php-ext-install pdo_dblib

RUN docker-php-ext-configure opcache --enable-opcache && \
    docker-php-ext-install pdo pdo_mysql zip

# Enable pcntl
RUN docker-php-ext-configure pcntl --enable-pcntl && docker-php-ext-install pcntl

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY docker/php/conf.d/opcache.ini /usr/local/etc/php/conf.d/opcache.ini



WORKDIR /var/www/html

COPY docker/000-default.conf /etc/apache2/sites-available/000-default.conf

# Install Laravel
RUN composer create-project --prefer-dist laravel/laravel /var/www/html

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache



RUN npm install -g @angular/cli

RUN a2enmod rewrite
