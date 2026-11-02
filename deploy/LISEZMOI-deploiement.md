# Déploiement de MediGest sur le serveur de Dukani

MediGest tourne sur le serveur de Dukani (EC2 `13.49.80.9`, 2 Go de RAM) et
réutilise ce qui y est déjà :

| Élément | Où |
|---|---|
| Base de données | PostgreSQL 16 de Dukani (`dukani_postgres`), base et utilisateur `medigest` |
| HTTPS / certificat | Caddy de Dukani (`dukani_caddy`), qui transmet à `medigest_web` |
| Réseau | `dukani_default` (réseau du compose Dukani) |
| Fichiers MediGest | `~/medigest/` sur le serveur |

```
Internet ──443──> dukani_caddy ──HTTP──> medigest_web (Caddy) ──FastCGI──> medigest_app (php-fpm)
                                                                              │
                                                     medigest_queue ──────────┼──> dukani_postgres (base medigest)
```

## Déjà fait (2 novembre 2026)

- Utilisateur et base `medigest` créés dans `dukani_postgres`.
- Mot de passe généré sur le serveur : `~/medigest/.db_password` (lisible par `ubuntu` seul).
- L'utilisateur `medigest` ne peut se connecter ni à `dukani_db` ni à `postgres`.
- DNS : `medigest.dukani.tech` → `13.49.80.9`.

## Premier déploiement

### 1. Construire l'image (sur le poste de développement)

Docker Desktop doit être lancé.

```bash
docker build -t medigest-app:latest .
docker save medigest-app:latest | gzip > medigest-app.tar.gz
md5sum medigest-app.tar.gz
```

### 2. Envoyer l'image et la configuration

```bash
scp -i "D:/02-Dev/Cle SSH/adminbani.pem" medigest-app.tar.gz deploy/compose.yaml deploy/Caddyfile deploy/backup-db.sh .env.production.example ubuntu@13.49.80.9:~/medigest/
```

La connexion coupe parfois en plein envoi : comparer le `md5sum` des deux côtés et relancer si besoin.

### 3. Sur le serveur : charger l'image et préparer le `.env`

```bash
cd ~/medigest
md5sum medigest-app.tar.gz          # doit correspondre au poste
gunzip -c medigest-app.tar.gz | sudo docker load

cp .env.production.example .env
chmod 600 .env
# Mot de passe de la base, sans le recopier à la main
sed -i "s|^DB_PASSWORD=.*|DB_PASSWORD=$(cat .db_password)|" .env
# Clé de l'application
sed -i "s|^APP_KEY=.*|APP_KEY=$(sudo docker run --rm --entrypoint php medigest-app:latest artisan key:generate --show)|" .env
nano .env                           # compléter la partie MAIL_*
```

### 4. Démarrer MediGest

```bash
cd ~/medigest
sudo docker compose -p medigest up -d
sudo docker compose -p medigest logs -f medigest-app   # migrations puis « php-fpm ready »
```

Au démarrage, `medigest_app` attend la base, applique les migrations puis les données de référence.

### 5. Brancher le domaine dans le Caddy de Dukani

Ajouter à la fin de `~/dukani/Caddyfile` :

```
medigest.dukani.tech {
	reverse_proxy medigest_web:80
	header Strict-Transport-Security "max-age=31536000; includeSubDomains"
}
```

Puis recharger Caddy sans couper Dukani :

```bash
sudo docker exec dukani_caddy caddy reload --config /etc/caddy/Caddyfile
```

Le certificat est obtenu automatiquement à la première visite de https://medigest.dukani.tech.

### 6. Sauvegarde nocturne

```bash
chmod +x ~/medigest/backup-db.sh
~/medigest/backup-db.sh             # test immédiat
crontab -e                          # ajouter la ligne suivante (après celle de Dukani)
```

```
45 2 * * * $HOME/medigest/backup-db.sh >> $HOME/medigest/sauvegardes/backup.log 2>&1
```

La base est sauvegardée, mais pas les fichiers déposés dans l'application (volume `medigest_storage_data`).

## Mises à jour suivantes

Refaire les étapes 1 et 2 (image seulement), puis :

```bash
cd ~/medigest
gunzip -c medigest-app.tar.gz | sudo docker load
sudo docker compose -p medigest up -d
```

Faire une sauvegarde avant toute mise à jour qui contient des migrations : `~/medigest/backup-db.sh`.

## À savoir

- **Postgres partagé.** Redémarrer `dukani_postgres` (ou faire un `down` du compose Dukani) coupe aussi MediGest.
- **Mémoire.** Les conteneurs MediGest sont plafonnés (app 512 Mo, worker 256 Mo, Caddy 64 Mo) pour ne jamais priver Dukani de mémoire.
- **Ordre de démarrage.** MediGest a besoin du réseau `dukani_default` : le compose Dukani doit être lancé en premier.
