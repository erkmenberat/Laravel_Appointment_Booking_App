FROM php:8.4-fpm-alpine

# System-Abhängigkeiten
RUN apk add --no-cache \
    nginx \
    supervisor \
    nodejs \
    npm \
    curl \
    zip \
    unzip \
    git \
    oniguruma-dev \
    libxml2-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    mysql-client

# PHP-Extensions
RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    mbstring \
    xml \
    ctype \
    bcmath \
    tokenizer \
    fileinfo \
    opcache

# Composer installieren
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Working directory
WORKDIR /var/www/html

# Composer-Abhängigkeiten zuerst (Layer-Caching)
COPY composer.json composer.lock ./
RUN composer install --optimize-autoloader --no-dev --no-interaction --no-scripts

# NPM-Abhängigkeiten
COPY package.json package-lock.json ./
RUN npm ci

# Restlichen Code kopieren
COPY . .

# Frontend bauen
RUN npm run build

# Composer Scripts nachträglich ausführen
RUN composer run-script post-autoload-dump || true

# Storage & Cache Permissions
RUN mkdir -p storage/framework/{sessions,views,cache} \
    && mkdir -p storage/logs \
    && mkdir -p bootstrap/cache \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# Nginx Konfiguration
COPY docker/nginx.conf /etc/nginx/nginx.conf

# Supervisor Konfiguration
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# PHP OPcache Konfiguration für Production
RUN echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.memory_consumption=128" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.interned_strings_buffer=8" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.max_accelerated_files=10000" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.validate_timestamps=0" >> /usr/local/etc/php/conf.d/opcache.ini

EXPOSE 8080

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
