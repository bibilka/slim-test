FROM php:8.0-fpm

RUN apt update \
    && apt install -y zlib1g-dev g++ git libicu-dev zip libzip-dev zip libpq-dev libpng-dev libonig-dev \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install intl opcache pdo pdo_pgsql \
    && pecl install apcu \
    && docker-php-ext-enable apcu \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip

RUN docker-php-ext-install mysqli   

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install extensions
RUN docker-php-ext-install pdo_mysql mbstring zip exif pcntl
# RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install gd

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy composer.lock and composer.json
COPY composer.json /var/www

# Copy existing application directory contents
COPY . /var/www

# USER www-data
# RUN rm keys/private.key || true
# RUN rm keys/public.key || true
# RUN	openssl genrsa -out /var/www/keys/private.key 2048
# RUN	openssl rsa -in /var/www/keys/private.key -pubout -out keys/public.key
# RUN chown www-data:www-data /var/www/keys/public.key
# RUN chown www-data:www-data /var/www/keys/private.key
# RUN chmod 660 /var/www/keys/public.key
# RUN chmod 660 /var/www/keys/private.key

# Set working directory
WORKDIR /var/www

# Expose port 9000 and start php-fpm server
EXPOSE 9000

CMD ["php-fpm"]