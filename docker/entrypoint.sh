#!/bin/sh
set -e

# =============================================================================
# Préparation du conteneur applicatif avant de céder la main à php-fpm.
# Exécuté aussi par le worker de file d'attente, d'où les garde-fous : seul le
# conteneur web applique les migrations.
# =============================================================================

cd /var/www/html

# --- Dossier public partagé avec Caddy -------------------------------------
# Le volume masque le dossier de l'image : il est resynchronisé depuis la copie
# de référence, sinon un redéploiement servirait les assets de la version
# précédente. --delete retire les fichiers disparus entre deux versions.
if [ -d /opt/public-src ]; then
    rsync -a --delete /opt/public-src/ /var/www/html/public/
fi

# --- Arborescence de stockage ----------------------------------------------
# Le volume persistant peut être vide au premier démarrage.
mkdir -p \
    storage/app/public \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# --- Attente de la base ----------------------------------------------------
# Test via PDO plutot qu avec un client externe : MySQL 8.4 utilise par defaut
# caching_sha2_password, que le client MariaDB ne sait pas negocier.
printf 'Attente de la base de donnees'
i=0
until php -r '
    try {
        new PDO(
            sprintf("mysql:host=%s;port=%s", getenv("DB_HOST"), getenv("DB_PORT") ?: 3306),
            getenv("DB_USERNAME"),
            getenv("DB_PASSWORD")
        );
        exit(0);
    } catch (Throwable $e) {
        exit(1);
    }
' 2>/dev/null; do
    i=$((i + 1))
    if [ "$i" -ge 60 ]; then
        echo ' : injoignable apres 60 tentatives, abandon.' >&2
        exit 1
    fi
    printf '.'
    sleep 2
done
echo ' : disponible.'

# --- Migrations et données de référence ------------------------------------
# Réservé au conteneur web pour éviter que le worker ne migre en parallèle.
if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
    echo 'Application des migrations...'
    php artisan migrate --force --no-interaction

    echo 'Mise en place des donnees de reference...'
    php artisan db:seed --force --no-interaction
fi

# --- Caches de production --------------------------------------------------
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Lien public/storage vers storage/app/public
php artisan storage:link --quiet || true

exec "$@"
