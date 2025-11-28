# Multi-stage build pour optimiser la taille de l'image finale
# Stage 1 : Builder - Composer et dépendances PHP
FROM php:8.2-fpm AS builder

WORKDIR /app

# Install build dependencies (Debian-based image)
RUN apt-get update && DEBIAN_FRONTEND=noninteractive apt-get install -y --no-install-recommends \
    build-essential \
    ca-certificates \
    curl \
    git \
    libfreetype-dev \
    libjpeg-dev \
    libonig-dev \
    libpng-dev \
    libpq-dev \
    libsqlite3-dev \
    libwebp-dev \
    libxml2-dev \
    libzip-dev \
    nodejs \
    npm \
    pkg-config \
    unzip \
    zip \
 && rm -rf /var/lib/apt/lists/*

# Configure and install PHP extensions
RUN docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
        --with-webp && \
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
        xml \
        zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy composer files and install dependencies
COPY composer.json composer.lock ./
RUN composer install --no-scripts --no-interaction --prefer-dist --optimize-autoloader

# Copy project and prepare assets
COPY . .
RUN composer dump-autoload --optimize && \
    npm ci && npm run build

# Stage 2 : Runtime - Image finale
FROM php:8.2-fpm

WORKDIR /app

# Install runtime dependencies (Debian)
RUN apt-get update && DEBIAN_FRONTEND=noninteractive apt-get install -y --no-install-recommends \
    ca-certificates \
    curl \
    libfreetype6 \
    libjpeg62-turbo \
    libonig5 \
    libpng16-16 \
    libwebp7 \
    libxml2 \
    libzip5 \
    zlib1g \
 && rm -rf /var/lib/apt/lists/*

# Copy PHP extensions from builder
COPY --from=builder /usr/local/lib/php/extensions /usr/local/lib/php/extensions
COPY --from=builder /usr/local/etc/php/conf.d /usr/local/etc/php/conf.d

# Copy built app from builder
COPY --from=builder /app /app

# Create necessary storage directories
RUN mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/views \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Copy entrypoint script
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Expose port 8000
EXPOSE 8000

# Health check
HEALTHCHECK --interval=30s --timeout=10s --start-period=40s --retries=3 \
    CMD curl -f http://localhost:8000/health || exit 1

# Use www-data user
USER www-data

# Entrypoint
ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
