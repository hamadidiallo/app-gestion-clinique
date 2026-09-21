# syntax=docker/dockerfile:1

# =============================================================================
# Étape 1 : compilation des assets front (Vite + Tailwind)
# public/build n'est pas versionné, il doit donc être produit ici.
# =============================================================================
FROM node:22-alpine AS assets

WORKDIR /app

# Les dépendances sont installées avant le code pour profiter du cache Docker
COPY package.json package-lock.json ./
RUN npm ci --ignore-scripts

COPY vite.config.js ./
COPY resources ./resources
RUN npm run build

# =============================================================================
# Étape 2 : dépendances PHP de production
# =============================================================================
FROM composer:2 AS vendor

WORKDIR /app

COPY composer.json composer.lock ./

# --no-scripts : les commandes artisan ne peuvent pas tourner sans le code complet
RUN composer install \
        --no-dev \
        --no-scripts \
        --no-autoloader \
        --prefer-dist \
        --no-interaction

COPY . .

RUN composer dump-autoload --optimize --no-dev

# =============================================================================
# Étape 3 : image d'exécution
# =============================================================================
FROM php:8.5-fpm-alpine AS runtime

# Dépendances système : rsync sert à repeupler le volume public à chaque démarrage
RUN apk add --no-cache \
        rsync \
        icu-libs \
        libzip \
        oniguruma \
    && apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
        icu-dev \
        libzip-dev \
        oniguruma-dev \
    # opcache est déjà compilé dans l'image officielle : l'inclure ici ferait
    # échouer l'installation, faute de module produit. Il est simplement
    # configuré via docker/php.ini.
    && docker-php-ext-install -j"$(nproc)" \
        pdo_mysql \
        bcmath \
        intl \
        zip \
        pcntl \
    && apk del .build-deps

COPY docker/php.ini /usr/local/etc/php/conf.d/99-app.ini
COPY docker/php-fpm.conf /usr/local/etc/php-fpm.d/zz-app.conf

WORKDIR /var/www/html

# Code applicatif et dépendances déjà résolues
COPY --chown=www-data:www-data . .
COPY --from=vendor --chown=www-data:www-data /app/vendor ./vendor
COPY --from=assets --chown=www-data:www-data /app/public/build ./public/build

# Copie de référence du dossier public : le volume partagé avec Caddy est
# resynchronisé depuis cette copie à chaque démarrage, sinon un redéploiement
# laisserait les anciens assets en place.
RUN cp -a public /opt/public-src \
    && mkdir -p \
        storage/app/public \
        storage/framework/cache/data \
        storage/framework/sessions \
        storage/framework/views \
        storage/logs \
        bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

COPY docker/entrypoint.sh /usr/local/bin/entrypoint
# Les retours chariot d'un poste Windows rendraient le shebang illisible
RUN sed -i 's/\r$//' /usr/local/bin/entrypoint \
    && chmod +x /usr/local/bin/entrypoint

EXPOSE 9000

ENTRYPOINT ["entrypoint"]
CMD ["php-fpm"]
