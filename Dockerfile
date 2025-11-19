# Multi-stage build pour optimiser la taille de l'image finale
# Stage 1 : Builder - Composer et dépendances PHP
FROM php:8.2-fpm-alpine AS builder

WORKDIR /app

# Installer toutes les dépendances en une seule commande RUN (optimisation)
RUN apk add --update --no-cache \
    bash \
    build-base \
    curl \
    freetype-dev \
    git \
    libjpeg-turbo-dev \
    libpng-dev \
    libxml2-dev \
    mysql-dev \
    nodejs \
    npm \
    oniguruma-dev \
    postgresql-dev \
    readline-dev \
    sqlite-dev \
    unzip \
    zip \
    zlib-dev

# Configurer et installer les extensions PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install -j$(nproc) \
        bcmath \
        ctype \
        fileinfo \
        gd \
        json \
        mbstring \
        pdo \
        pdo_mysql \
        pdo_pgsql \
        pdo_sqlite \
        tokenizer \
        xml

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copier les fichiers composer et installer les dépendances
COPY composer.json composer.lock ./
RUN composer install --no-scripts --no-interaction --prefer-dist --optimize-autoloader

# Copier le projet et préparer les assets
COPY . .
RUN composer dump-autoload --optimize && \
    npm ci && npm run build

# Stage 2 : Runtime - Image finale
FROM php:8.2-fpm-alpine

WORKDIR /app

# Installer uniquement les dépendances runtime requises
RUN apk add --update --no-cache \
    bash \
    curl \
    freetype \
    libjpeg-turbo \
    libpng \
    libxml2 \
    mysql-client \
    oniguruma \
    postgresql-client \
    readline \
    zlib

# Installer uniquement les extensions PHP runtime nécessaires
# IMPORTANT: Les extensions doivent être réinstallées car les .so ne sont pas copiées du builder
RUN apk add --update --no-cache --virtual .build-deps \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    libxml2-dev \
    mysql-dev \
    oniguruma-dev \
    postgresql-dev \
    sqlite-dev \
    zlib-dev && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install -j$(nproc) \
        bcmath \
        ctype \
        fileinfo \
        gd \
        json \
        mbstring \
        pdo \
        pdo_mysql \
        pdo_pgsql \
        pdo_sqlite \
        tokenizer \
        xml && \
    apk del .build-deps

# Copier les fichiers compilés et dépendances du builder
COPY --from=builder /app /app

# Créer les répertoires de stockage nécessaires
RUN mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/views \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Copier le script d'entrée
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Exposer le port 8000
EXPOSE 8000

# Health check
HEALTHCHECK --interval=30s --timeout=10s --start-period=40s --retries=3 \
    CMD curl -f http://localhost:8000/health || exit 1

# Utiliser l'utilisateur www-data
USER www-data

# Commande d'entrée
ENTRYPOINT ["docker-entrypoint.sh"]

# Commande par défaut : lancer Laravel
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
