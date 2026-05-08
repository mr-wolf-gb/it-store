FROM node:22-alpine AS frontend-builder

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci --no-audit --no-fund

COPY resources ./resources
COPY public ./public
COPY vite.config.js postcss.config.js tailwind.config.js ./

RUN npm run build

FROM composer:2 AS vendor-builder

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-progress \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts

COPY . .

RUN composer dump-autoload --optimize --classmap-authoritative --no-dev

FROM php:8.3-apache AS app

WORKDIR /var/www/html

RUN apt-get update \
    && apt-get install -y --no-install-recommends libsqlite3-dev unzip git \
    && docker-php-ext-install pdo_sqlite bcmath \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/*

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri -e "s!/var/www/html!${APACHE_DOCUMENT_ROOT}!g" /etc/apache2/sites-available/*.conf \
    && sed -ri -e "s!/var/www/!${APACHE_DOCUMENT_ROOT}!g" /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

COPY --from=vendor-builder /app /var/www/html
COPY --from=frontend-builder /app/public/build /var/www/html/public/build
COPY docker/entrypoint.sh /usr/local/bin/it-store-entrypoint
COPY docker/php/uploads.ini /usr/local/etc/php/conf.d/99-uploads.ini
COPY docker/apache/large-uploads.conf /etc/apache2/conf-available/large-uploads.conf

RUN a2enconf large-uploads

RUN mkdir -p storage bootstrap/cache database \
    && chown -R www-data:www-data storage bootstrap/cache database \
    && chmod +x /usr/local/bin/it-store-entrypoint \
    && chown root:root /usr/local/etc/php/conf.d/99-uploads.ini \
    && chmod 644 /usr/local/etc/php/conf.d/99-uploads.ini

ENTRYPOINT ["it-store-entrypoint"]
CMD ["apache2-foreground"]
