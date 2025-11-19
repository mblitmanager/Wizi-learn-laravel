#!/bin/bash
set -e

# Attendre que la base de données soit prête
if [ ! -z "$DB_HOST" ]; then
    echo "Attente de la connexion à la base de données..."
    while ! nc -z "$DB_HOST" "${DB_PORT:-3306}"; do
        sleep 1
    done
    echo "Base de données disponible"
fi

# Générer la clé d'application si elle n'existe pas
if [ -z "$APP_KEY" ]; then
    echo "Génération de la clé d'application..."
    php artisan key:generate
fi

# Exécuter les migrations
if [ "$RUN_MIGRATIONS" = "true" ]; then
    echo "Exécution des migrations..."
    php artisan migrate --force
fi

# Exécuter les seeders en développement
if [ "$APP_ENV" = "local" ] && [ "$RUN_SEEDS" = "true" ]; then
    echo "Exécution des seeders..."
    php artisan db:seed
fi

# Optimiser l'application
echo "Optimisation de l'application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Nettoyer les fichiers temporaires
php artisan storage:link 2>/dev/null || true

# Exécuter la commande passée
exec "$@"
