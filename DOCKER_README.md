# 🐳 Configuration Docker - Wizi Learn Laravel

## Vue d'ensemble

Ce projet est entièrement configuré pour fonctionner avec Docker, tant en développement local qu'en production sur **Google Cloud Run**.

## 📁 Fichiers de configuration

| Fichier | Description |
|---------|-------------|
| `Dockerfile` | Image multi-stage optimisée pour Cloud Run |
| `docker-compose.yml` | Configuration complète pour développement local |
| `.dockerignore` | Fichiers à exclure du build Docker |
| `cloudbuild.yaml` | CI/CD automatique avec Google Cloud Build |
| `docker-entrypoint.sh` | Script d'initialisation des conteneurs |
| `.env.docker.example` | Variables d'environnement exemple |
| `DOCKER_DEPLOY.md` | Guide complet de déploiement |
| `deploy-to-cloudrun.sh` | Script de déploiement (Linux/Mac) |
| `deploy-to-cloudrun.ps1` | Script de déploiement (Windows PowerShell) |
| `Makefile` | Commandes raccourcies Docker |

## 🚀 Démarrage rapide

### 1. Installation locale (Docker)

```powershell
# Copier le fichier .env
copy .env.docker.example .env.local

# Démarrer les conteneurs
docker-compose up -d

# Initialiser la base de données
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed
```

### 2. Accéder à l'application

- **API** : http://localhost:8000
- **Base de données** : localhost:3306 (user: `wizi`, password: `password`)
- **Redis** : localhost:6379
- **MailHog** : http://localhost:8025

## 📝 Services Docker inclus

### Services principaux

| Service | Image | Port | Utilisé pour |
|---------|-------|------|--------------|
| **app** | PHP 8.2 FPM Alpine | 8000 | Application Laravel |
| **mysql** | MySQL 8.0 | 3306 | Base de données |
| **redis** | Redis 7 Alpine | 6379 | Cache & Queue |
| **mailhog** | MailHog | 8025 | Test des emails |
| **node** | Node.js 20 Alpine | - | Compilation des assets |

### Avantages du setup

✅ **Multi-stage build** : Image finale optimisée (~500MB)  
✅ **Alpine Linux** : Image minimale et rapide  
✅ **Volumes persistants** : Données conservées entre les redémarrages  
✅ **Health checks** : Conteneurs vérifiés automatiquement  
✅ **Réseaux isolés** : Communication sécurisée entre services  
✅ **Environment variables** : Configuration centralisée  

## 🎯 Commandes courantes

### Utiliser le Makefile (recommandé)

```powershell
# Afficher toutes les commandes disponibles
make help

# Installation complète
make install

# Gestion des conteneurs
make up
make down
make restart
make logs

# Base de données
make migrate
make seed
make db-fresh

# Accès aux outils
make shell          # Bash dans le conteneur app
make tinker         # Laravel Tinker REPL
make db-shell       # Shell MySQL
make redis-shell    # Shell Redis

# Cache
make cache-clear
make cache-optimize

# Tests
make test
```

### Utiliser docker-compose directement

```powershell
# Démarrer
docker-compose up -d

# Afficher le statut
docker-compose ps

# Voir les logs
docker-compose logs -f app

# Exécuter une commande
docker-compose exec app php artisan tinker

# Arrêter
docker-compose stop

# Supprimer tout (y compris les données)
docker-compose down -v
```

## ⚙️ Configuration des variables d'environnement

### Fichier `.env.local` (développement)

```bash
APP_ENV=local
APP_DEBUG=true
DB_HOST=mysql
DB_PASSWORD=password
CACHE_DRIVER=redis
REDIS_HOST=redis
JWT_SECRET=your_jwt_secret
```

### Fichier `.env.cloud` (production - Google Cloud Run)

```bash
APP_ENV=production
APP_DEBUG=false
DB_HOST=<CLOUD_SQL_PRIVATE_IP>
DB_PASSWORD=<SECURE_PASSWORD>
REDIS_HOST=<REDIS_PRIVATE_IP>
JWT_SECRET=<PRODUCTION_JWT_SECRET>
LOG_CHANNEL=stderr
```

## 🔐 Variables d'environnement requises

| Variable | Local | Cloud | Description |
|----------|-------|-------|-------------|
| `APP_KEY` | ✅ | ✅ | Clé de chiffrement (générer avec `php artisan key:generate`) |
| `JWT_SECRET` | ✅ | ✅ | Secret JWT (générer avec `openssl rand -hex 32`) |
| `DB_PASSWORD` | ✅ | ✅ | Mot de passe MySQL |
| `REDIS_PASSWORD` | ⚪ | ✅ | Mot de passe Redis (optionnel en local) |

## 🚀 Déploiement sur Google Cloud Run

### Prérequis

```powershell
# Installer Google Cloud CLI
# https://cloud.google.com/sdk/docs/install

# Initialiser gcloud
gcloud init

# S'authentifier
gcloud auth login

# Activer les APIs
gcloud services enable run.googleapis.com
gcloud services enable cloudbuild.googleapis.com
```

### Déploiement manuel

**Option 1 : Script PowerShell (Windows)**
```powershell
.\deploy-to-cloudrun.ps1 -ProjectId "your-project-id" `
                         -ServiceName "wizi-learn-api" `
                         -Region "europe-west1"
```

**Option 2 : Script Bash (Linux/Mac)**
```bash
chmod +x deploy-to-cloudrun.sh
./deploy-to-cloudrun.sh your-project-id wizi-learn-api europe-west1
```

**Option 3 : Commande gcloud directe**
```bash
docker build -t gcr.io/YOUR_PROJECT/wizi-learn-api:latest .
docker push gcr.io/YOUR_PROJECT/wizi-learn-api:latest

gcloud run deploy wizi-learn-api \
    --image=gcr.io/YOUR_PROJECT/wizi-learn-api:latest \
    --region=europe-west1 \
    --memory=512Mi \
    --cpu=1 \
    --env-vars-file=.env.cloud
```

### Déploiement automatique avec Cloud Build

1. Connecter votre repository GitHub :
```bash
gcloud builds connect --repository-name Wizi-learn-laravel \
    --repository-owner YOUR_GITHUB_USERNAME \
    --region europe-west1
```

2. Créer un trigger :
```bash
gcloud builds triggers create github \
    --name wizi-learn-deploy \
    --repo-name Wizi-learn-laravel \
    --repo-owner YOUR_GITHUB_USERNAME \
    --branch-pattern "^main$" \
    --build-config cloudbuild.yaml
```

Le fichier `cloudbuild.yaml` déploiera automatiquement à chaque push sur `main`.

## 🔍 Monitoring et Debugging

### Logs locaux

```powershell
# Voir tous les logs
docker-compose logs

# Voir les logs d'un service spécifique
docker-compose logs -f app
docker-compose logs -f mysql
docker-compose logs -f redis

# Voir les 100 dernières lignes en temps réel
docker-compose logs -f --tail=100 app

# Voir les logs passés
docker-compose logs app | Select-Object -Last 50
```

### Logs Cloud Run

```bash
# Voir les logs en temps réel
gcloud logging read "resource.type=cloud_run_revision AND resource.labels.service_name=wizi-learn-api" \
    --limit=50 --format='value(timestamp,severity,textPayload)' --sort-by='-timestamp'

# Via Cloud Console
# https://console.cloud.google.com/run
```

### Accès aux conteneurs

```powershell
# Shell dans le conteneur app
docker-compose exec app bash

# MySQL Shell
docker-compose exec mysql mysql -u wizi -p wizi_learn

# Redis CLI
docker-compose exec redis redis-cli

# Monitoring Redis
docker-compose exec redis redis-cli monitor
```

## 🐛 Troubleshooting

### Port déjà utilisé

```powershell
# Trouver quel processus utilise le port 3306
netstat -ano | findstr :3306

# Ou changer le port dans docker-compose.yml
# ports:
#   - "3307:3306"
```

### Erreur de permissions sur storage/

Le Dockerfile résout ce problème automatiquement. Si vous rencontrez une erreur :

```powershell
docker-compose exec app chmod -R 775 storage bootstrap/cache
```

### Les migrations ne s'exécutent pas

```powershell
# Vérifier que RUN_MIGRATIONS=true dans docker-compose.yml
# Ou exécuter manuellement :
docker-compose exec app php artisan migrate --force
```

### Conteneur MySQL qui ne démarre pas

```powershell
# Vérifier les logs
docker-compose logs mysql

# Supprimer le volume et recommencer
docker-compose down -v
docker-compose up -d
docker-compose exec app php artisan migrate
```

## 📚 Documentation supplémentaire

- [Guide complet de déploiement](DOCKER_DEPLOY.md)
- [Documentation Laravel](https://laravel.com/docs)
- [Google Cloud Run](https://cloud.google.com/run/docs)
- [Docker Compose](https://docs.docker.com/compose/)

## 🔒 Sécurité

✅ Les secrets ne doivent JAMAIS être commités  
✅ Utiliser Google Secret Manager en production  
✅ HTTPS activé automatiquement sur Cloud Run  
✅ Validation des variables d'environnement requises  
✅ Logs centralisés via Cloud Logging  

## 📊 Architecture

```
┌─────────────────────────────────────┐
│      Développement Local (Docker)    │
├─────────────────────────────────────┤
│  App (PHP 8.2)   │   Node (Assets)   │
│  MySQL (8.0)     │   Redis (Cache)   │
│  MailHog (Test)  │   Network         │
└─────────────────────────────────────┘
           ↓ (git push main)
┌─────────────────────────────────────┐
│     Google Cloud Build (CI/CD)       │
│  - Build image Docker               │
│  - Push vers GCR                    │
│  - Deploy sur Cloud Run             │
└─────────────────────────────────────┘
           ↓
┌─────────────────────────────────────┐
│  Production (Google Cloud Run)       │
├─────────────────────────────────────┤
│  Cloud Run Service                   │
│  - Auto-scaling                      │
│  - Load Balancing                    │
│  - HTTPS                             │
├─────────────────────────────────────┤
│  Cloud SQL (MySQL)                   │
│  - Managed Database                  │
│  - Backups automatiques              │
├─────────────────────────────────────┤
│  Cloud Memorystore (Redis)           │
│  - Cache & Queue                     │
│  - High Availability                 │
├─────────────────────────────────────┤
│  Cloud Logging & Monitoring          │
│  - Logs centralisés                  │
│  - Alertes & Métriques               │
└─────────────────────────────────────┘
```

## ✅ Checklist

- [ ] Docker Desktop installé
- [ ] `.env.local` créé avec les bonnes variables
- [ ] `docker-compose up -d` réussi
- [ ] `docker-compose exec app php artisan migrate` réussi
- [ ] Application accessible sur http://localhost:8000
- [ ] Tests passent localement (`make test`)
- [ ] Prêt à déployer sur Cloud Run ! 🚀

---

**Version** : 1.0  
**Dernière mise à jour** : Novembre 2025  
**Auteur** : GitHub Copilot
