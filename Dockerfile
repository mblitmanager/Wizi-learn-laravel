# Multi-stage build pour optimiser la taille de l'image finale
# Stage 1 : Builder - Composer et dépendances PHP
FROM php:8.2-fpm-alpine AS builder

WORKDIR /app

# Installer les dépendances système nécessaires pour les extensions PHP
RUN apk add --no-cache \
    bash \
    build-base \
    curl \
    freetype-dev \
    git \
    libjpeg-turbo-dev \
    libpng-dev \
    libxml2-dev \
    mysql-dev \
    oniguruma-dev \
    postgresql-dev \
    readline-dev \
    sqlite-dev \
    unzip \
    zip \
    zlib-dev

# Installer les extensions PHP avec les dépendances correctes
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

# Installer Node.js et npm pour Vite/assets
RUN apk add --no-cache nodejs npm

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copier les fichiers composer
COPY composer.json composer.lock ./

# Installer les dépendances PHP (sans scripts)
RUN composer install --no-scripts --no-interaction --prefer-dist --optimize-autoloader

# Copier tout le projet
COPY . .

# Générer l'autoloader optimisé
RUN composer dump-autoload --optimize

# Installer les dépendances Node et compiler les assets
RUN npm ci && npm run build

# Stage 2 : Runtime - Image finale
FROM php:8.2-fpm-alpine

WORKDIR /app

# Installer les dépendances runtime (alphabétiquement)
RUN apk add --no-cache \
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

# Installer les extensions PHP avec configuration correcte
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
