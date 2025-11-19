# Multi-stage build pour optimiser la taille de l'image finale
# Stage 1 : Builder - Composer et dépendances PHP
FROM php:8.2-fpm-alpine AS builder

WORKDIR /app

# Installer les dépendances système
RUN apk add --no-cache \
    curl \
    git \
    zip \
    unzip \
    build-base \
    oniguruma-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    postgresql-dev \
    mysql-dev \
    sqlite-dev \
    libxml2-dev \
    readline-dev

# Installer les extensions PHP nécessaires
RUN docker-php-ext-install -j$(nproc) \
    bcmath \
    ctype \
    fileinfo \
    json \
    mbstring \
    pdo \
    pdo_mysql \
    pdo_pgsql \
    pdo_sqlite \
    tokenizer \
    xml \
    gd

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

# Installer uniquement les dépendances runtime
RUN apk add --no-cache \
    mysql-client \
    postgresql-client \
    curl \
    bash \
    libpng \
    libjpeg-turbo \
    freetype \
    oniguruma \
    libxml2 \
    readline

# Installer les extensions PHP nécessaires
RUN docker-php-ext-install -j$(nproc) \
    bcmath \
    ctype \
    fileinfo \
    json \
    mbstring \
    pdo \
    pdo_mysql \
    pdo_pgsql \
    pdo_sqlite \
    tokenizer \
    xml \
    gd

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
