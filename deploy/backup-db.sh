#!/usr/bin/env bash
# Sauvegarde nocturne de la base MediGest, hébergée dans le PostgreSQL de Dukani.
# À installer dans ~/medigest/ sur le serveur, lancée par cron (voir LISEZMOI-deploiement.md).
# Garde 14 jours. Les sauvegardes restent sur ce serveur : en copier régulièrement ailleurs.
set -euo pipefail
cd "$HOME/medigest"
mkdir -p sauvegardes
# Le superutilisateur de Dukani lit la base medigest par le socket local du conteneur :
# aucun mot de passe MediGest n'est nécessaire ici.
DBU=$(grep -E "^DB_USER=" "$HOME/dukani/.env" | cut -d= -f2- | tr -d "\"\r")
f="sauvegardes/auto_$(date +%Y%m%d_%H%M).sql.gz"
docker exec dukani_postgres pg_dump -U "$DBU" medigest | gzip > "$f.tmp"
mv "$f.tmp" "$f"
find sauvegardes -name 'auto_*.sql.gz' -mtime +14 -delete
echo "$(date '+%F %T') sauvegarde $f ($(du -h "$f" | cut -f1))"
