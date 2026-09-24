# Guide Complet de Déploiement Docker - Wizi Learn Laravel

## 📋 Table des matières

1. [Configuration locale avec Docker Compose](#configuration-locale)
2. [Déploiement sur Google Cloud Run](#déploiement-cloud-run)
3. [Variables d'environnement](#variables-denvironnement)
4. [Commandes utiles](#commandes-utiles)
5. [Troubleshooting](#troubleshooting)

---

## 🚀 Configuration Locale

### Prérequis

- Docker Desktop installé (https://www.docker.com/products/docker-desktop)
- Docker Compose (inclus dans Docker Desktop)
- 4 GB RAM minimum

### Démarrage rapide

#### 1. Cloner et configurer le projet

```powershell
cd c:\laragon\www\cursor\Wizi-learn-laravel
copy .env.example .env
```

#### 2. Créer un fichier `.env.local` avec vos variables

```bash
APP_NAME="Wizi Learn"
APP_ENV=local
APP_DEBUG=true
APP_KEY=base64:YOUR_KEY_HERE
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=wizi_learn
DB_USERNAME=wizi
DB_PASSWORD=your_secure_password

CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
REDIS_HOST=redis
REDIS_PORT=6379

JWT_SECRET=your_jwt_secret_key_here
```

#### 3. Lancer les conteneurs

```powershell
# Démarrer en arrière-plan
docker-compose up -d

# Voir les logs en temps réel
docker-compose logs -f app

# Ou lancer en mode attached (Ctrl+C pour arrêter)
docker-compose up
```

#### 4. Initialiser la base de données

```powershell
# Les migrations s'exécutent automatiquement au démarrage (RUN_MIGRATIONS=true)
# Pour exécuter manuellement :
docker-compose exec app php artisan migrate

# Seeder la base de données
docker-compose exec app php artisan db:seed
```

#### 5. Accéder à l'application

- **API Laravel** : http://localhost:8000
- **Base de données** : `localhost:3306`
- **Redis** : `localhost:6379`
- **MailHog (emails)** : http://localhost:8025

### Commandes Docker locales

```powershell
# Afficher le statut des conteneurs
docker-compose ps

# Arrêter les conteneurs
docker-compose stop

# Redémarrer les conteneurs
docker-compose restart

# Supprimer les conteneurs et volumes
docker-compose down
docker-compose down -v  # Avec données

# Voir les logs d'un service
docker-compose logs app
docker-compose logs mysql
docker-compose logs redis

# Exécuter une commande dans le conteneur
docker-compose exec app php artisan tinker
docker-compose exec mysql mysql -u wizi -p wizi_learn
docker-compose exec redis redis-cli
```

---

## ☁️ Déploiement sur Google Cloud Run

### Prérequis

- Compte Google Cloud (https://cloud.google.com/)
- Google Cloud CLI installé
- Projet GCP créé

### Architecture

```
┌─────────────────────────────────────┐
│      Google Cloud Run (Service)      │
│  - Auto-scaling (0-100 instances)   │
│  - Load Balancing                    │
│  - Logs & Monitoring                 │
└─────────────────────────────────────┘
          ↓
┌─────────────────────────────────────┐
│   Google Container Registry (GCR)    │
│   - Stocke les images Docker         │
│   - Versioning automatique           │
└─────────────────────────────────────┘
          ↓
┌─────────────────────────────────────┐
│   Cloud SQL (MySQL/PostgreSQL)       │
│   - Managed database                 │
│   - Backups automatiques             │
│   - High availability                │
└─────────────────────────────────────┘
          ↓
┌─────────────────────────────────────┐
│   Cloud Memorystore (Redis)          │
│   - Cache & Queue                    │
│   - High availability                │
└─────────────────────────────────────┘
```

### Étapes de déploiement

#### 1. Configuration initiale GCP

```bash
# Configurer gcloud CLI
gcloud init

# Définir le projet par défaut
gcloud config set project YOUR_PROJECT_ID

# Activer les APIs nécessaires
gcloud services enable run.googleapis.com
gcloud services enable cloudbuild.googleapis.com
gcloud services enable sql.googleapis.com
gcloud services enable memcache.googleapis.com
gcloud services enable container.googleapis.com
gcloud services enable artifactregistry.googleapis.com
```

#### 2. Créer les ressources Cloud SQL et Memorystore

```bash
# Créer une instance Cloud SQL MySQL
gcloud sql instances create wizi-learn-db \
  --database-version MYSQL_8_0 \
  --tier db-f1-micro \
  --region europe-west1 \
  --availability-type REGIONAL \
  --backup-start-time 03:00

# Créer une base de données
gcloud sql databases create wizi_learn \
  --instance wizi-learn-db

# Créer un utilisateur
gcloud sql users create wizi \
  --instance wizi-learn-db \
  --password YOUR_DB_PASSWORD

# Créer une instance Redis
gcloud memorystore redis instances create wizi-redis \
  --region europe-west1 \
  --size 1 \
  --redis-version 7.0
```

#### 3. Configuration des variables d'environnement

Créer un fichier `.env.cloud` avec les variables de production :

```bash
APP_NAME="Wizi Learn"
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:YOUR_PRODUCTION_KEY_HERE
APP_URL=https://YOUR_CLOUD_RUN_URL

# Base de données Cloud SQL
DB_CONNECTION=mysql
DB_HOST=CLOUD_SQL_PRIVATE_IP_OR_SOCKET
DB_PORT=3306
DB_DATABASE=wizi_learn
DB_USERNAME=wizi
DB_PASSWORD=YOUR_DB_PASSWORD
DB_UNIX_SOCKET=/cloudsql/PROJECT:REGION:INSTANCE

# Redis (Cloud Memorystore)
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
REDIS_HOST=REDIS_PRIVATE_IP
REDIS_PORT=6379
REDIS_PASSWORD=YOUR_REDIS_PASSWORD

# JWT
JWT_SECRET=your_production_jwt_secret

# Autres
LOG_CHANNEL=stderr
SENTRY_DSN=YOUR_SENTRY_DSN_OPTIONAL
```

#### 4. Déployer manuellement

```bash
# Construire l'image
docker build -t gcr.io/YOUR_PROJECT_ID/wizi-learn-api:latest .

# Pousser vers GCR
docker push gcr.io/YOUR_PROJECT_ID/wizi-learn-api:latest

# Déployer sur Cloud Run
gcloud run deploy wizi-learn-api \
  --image gcr.io/YOUR_PROJECT_ID/wizi-learn-api:latest \
  --region europe-west1 \
  --memory 512Mi \
  --cpu 1 \
  --timeout 900 \
  --max-instances 100 \
  --min-instances 1 \
  --allow-unauthenticated \
  --set-env-vars APP_ENV=production,APP_DEBUG=false,DB_HOST=CLOUD_SQL_IP \
  --add-cloudsql-instances PROJECT:REGION:INSTANCE_NAME
```

#### 5. Configuration du déploiement continu avec Cloud Build

```bash
# Connecter votre dépôt GitHub à Cloud Build
gcloud builds connect --repository-name Wizi-learn-laravel \
  --repository-owner YOUR_GITHUB_USERNAME \
  --region europe-west1

# Créer un trigger de build
gcloud builds triggers create github \
  --name wizi-learn-deploy \
  --repo-name Wizi-learn-laravel \
  --repo-owner YOUR_GITHUB_USERNAME \
  --branch-pattern "^main$" \
  --build-config cloudbuild.yaml \
  --region europe-west1
```

Le fichier `cloudbuild.yaml` déploiera automatiquement à chaque push sur `main`.

---

## 🔑 Variables d'Environnement

### Requises

| Variable | Description | Exemple |
|----------|-------------|---------|
| `APP_KEY` | Clé de chiffrement Laravel | `base64:xxxx...` |
| `JWT_SECRET` | Secret pour les tokens JWT | `votre_secret_jwt` |
| `DB_HOST` | Hôte de la base de données | `mysql` (local) ou IP (cloud) |
| `DB_PASSWORD` | Mot de passe MySQL | `secure_password` |

### Recommandées pour production

| Variable | Description | Valeur |
|----------|-------------|--------|
| `APP_ENV` | Environnement | `production` |
| `APP_DEBUG` | Mode debug | `false` |
| `LOG_CHANNEL` | Canal de logs | `stderr` (Cloud Run) |
| `SESSION_DRIVER` | Driver sessions | `redis` |
| `CACHE_DRIVER` | Driver cache | `redis` |

### Générer les clés secrètes

```bash
# Depuis le conteneur local
docker-compose exec app php artisan key:generate --show

# Depuis votre machine
php artisan key:generate --show

# JWT Secret
openssl rand -hex 32
```

---

## 📝 Commandes Utiles

### Gestion des migrations

```bash
# Exécuter les migrations
docker-compose exec app php artisan migrate

# Rollback
docker-compose exec app php artisan migrate:rollback

# Refresh (supprimer et recréer)
docker-compose exec app php artisan migrate:refresh

# Seed
docker-compose exec app php artisan db:seed
```

### Cache & Optimisation

```bash
# Vider les caches
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan view:clear

# Recacher (production)
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache
```

### Monitoring

```bash
# Logs Laravel
docker-compose logs -f app

# Logs MySQL
docker-compose logs -f mysql

# Logs Redis
docker-compose logs -f redis

# Accès au conteneur
docker-compose exec app bash
docker-compose exec mysql bash
```

### Artisan depuis le conteneur

```bash
# Tinker (REPL PHP)
docker-compose exec app php artisan tinker

# Lister les routes
docker-compose exec app php artisan route:list

# Tester une commande
docker-compose exec app php artisan queue:work
```

---

## 🐛 Troubleshooting

### Problème : "Connection refused" avec la base de données

**Solution** :
```bash
# Vérifier que le conteneur MySQL est prêt
docker-compose ps

# Attendre quelques secondes et réessayer
docker-compose exec app php artisan migrate
```

### Problème : Port 3306 déjà utilisé

**Solution** :
```bash
# Changer le port dans docker-compose.yml
ports:
  - "3307:3306"  # Au lieu de 3306

# Ou arrêter le service utilisant le port
netstat -ano | findstr :3306  # Windows
lsof -i :3306  # Mac/Linux
```

### Problème : Erreur de permissions sur storage/

**Solution** :
```bash
# Le Dockerfile définit les permissions correctement
# Si problème persiste :
docker-compose exec app chmod -R 775 storage bootstrap/cache
```

### Problème : Cloud Run timeout

**Solution** :
```bash
# Augmenter le timeout à 900 secondes (max)
gcloud run deploy wizi-learn-api \
  --timeout 900 \
  ...
```

### Problème : Migrations ne s'exécutent pas au démarrage

**Solution** :
```bash
# Vérifier que RUN_MIGRATIONS=true dans docker-compose.yml
# Ou exécuter manuellement
docker-compose exec app php artisan migrate --force
```

---

## 📊 Monitoring et Logs

### Cloud Run

```bash
# Voir les logs en temps réel
gcloud run services describe wizi-learn-api --region europe-west1

# Voir les logs détaillés
gcloud logging read "resource.type=cloud_run_revision AND resource.labels.service_name=wizi-learn-api" \
  --limit 50 --format json

# Monitor les métriques
# Accès via Cloud Console : Run > wizi-learn-api > Métriques
```

### Local

```bash
# Logs application
docker-compose logs -f --tail=100 app

# Logs système
docker stats

# Inspecting la base de données
docker-compose exec mysql mysql -u wizi -p wizi_learn
```

---

## 🔒 Sécurité

### Bonnes pratiques

1. ✅ **Ne pas commiter `.env`** en production
2. ✅ **Utiliser Google Secret Manager** pour les secrets
3. ✅ **Activer Cloud SQL Auth Proxy** pour les connexions sécurisées
4. ✅ **HTTPS obligatoire** (Cloud Run fourni automatiquement)
5. ✅ **API Keys et JWT secrets** générés de manière sécurisée
6. ✅ **Logs centralisés** via Cloud Logging

### Secret Manager

```bash
# Créer un secret
echo -n "YOUR_DB_PASSWORD" | gcloud secrets create db-password --data-file=-

# Accorder accès au service Cloud Run
gcloud secrets add-iam-policy-binding db-password \
  --member=serviceAccount:PROJECT_ID@appspot.gserviceaccount.com \
  --role=roles/secretmanager.secretAccessor

# Utiliser dans Cloud Run
gcloud run deploy wizi-learn-api \
  --set-secrets DB_PASSWORD=db-password:latest \
  ...
```

---

## 📚 Ressources

- [Laravel Docker Documentation](https://laravel.com/docs/deployment#docker)
- [Google Cloud Run Documentation](https://cloud.google.com/run/docs)
- [Cloud SQL Documentation](https://cloud.google.com/sql/docs)
- [Cloud Memorystore Documentation](https://cloud.google.com/memorystore/docs)
- [Best Practices Laravel on Cloud Run](https://cloud.google.com/run/docs/quickstarts/build-and-deploy/php)

---

## ✅ Checklist Déploiement

- [ ] `APP_KEY` généré
- [ ] `JWT_SECRET` généré
- [ ] `.env` configuré localement
- [ ] Migrations s'exécutent (`docker-compose exec app php artisan migrate`)
- [ ] Application accessible sur `http://localhost:8000`
- [ ] Tests passent localement
- [ ] Projet GCP créé
- [ ] APIs GCP activées
- [ ] Cloud SQL et Memorystore configurés
- [ ] Dépôt GitHub connecté à Cloud Build
- [ ] `cloudbuild.yaml` configuré
- [ ] Variables secrètes configurées dans Cloud Run
- [ ] Déploiement initial réussi
- [ ] Logs visibles dans Cloud Logging
- [ ] HTTPS fonctionne (auto-généré par Cloud Run)

---

**Version** : 1.0  
**Dernière mise à jour** : Novembre 2025  
**Auteur** : GitHub Copilot
