#!/bin/bash
# Script de déploiement sur Google Cloud Run
# Usage: ./deploy-to-cloudrun.sh [project-id] [service-name] [region]

set -e

# Couleurs pour l'output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Configuration par défaut
PROJECT_ID=${1:-}
SERVICE_NAME=${2:-"wizi-learn-api"}
REGION=${3:-"europe-west1"}
IMAGE_TAG="latest"

# Vérifications
if [ -z "$PROJECT_ID" ]; then
    echo -e "${RED}❌ Erreur : Project ID requis${NC}"
    echo "Usage: ./deploy-to-cloudrun.sh <project-id> [service-name] [region]"
    exit 1
fi

echo -e "${YELLOW}🚀 Déploiement sur Google Cloud Run${NC}"
echo "Project ID: $PROJECT_ID"
echo "Service: $SERVICE_NAME"
echo "Region: $REGION"
echo ""

# Configurer gcloud
echo -e "${YELLOW}📋 Configuration de gcloud...${NC}"
gcloud config set project $PROJECT_ID

# Vérifier que l'utilisateur est authentifié
echo -e "${YELLOW}🔐 Vérification de l'authentification...${NC}"
gcloud auth list

# Activer les APIs si nécessaire
echo -e "${YELLOW}⚙️  Activation des APIs Google Cloud...${NC}"
gcloud services enable run.googleapis.com
gcloud services enable cloudbuild.googleapis.com
gcloud services enable container.googleapis.com
gcloud services enable artifactregistry.googleapis.com

# Build l'image
echo -e "${YELLOW}🔨 Construction de l'image Docker...${NC}"
docker build -t gcr.io/$PROJECT_ID/$SERVICE_NAME:$IMAGE_TAG .
docker build -t gcr.io/$PROJECT_ID/$SERVICE_NAME:$(git rev-parse --short HEAD) .

# Push l'image vers GCR
echo -e "${YELLOW}📤 Push de l'image vers Google Container Registry...${NC}"
docker push gcr.io/$PROJECT_ID/$SERVICE_NAME:$IMAGE_TAG
docker push gcr.io/$PROJECT_ID/$SERVICE_NAME:$(git rev-parse --short HEAD)

# Déployer sur Cloud Run
echo -e "${YELLOW}☁️  Déploiement sur Cloud Run...${NC}"

# Récupérer les variables d'environnement depuis le .env
if [ ! -f ".env" ]; then
    echo -e "${RED}❌ Fichier .env non trouvé${NC}"
    exit 1
fi

# Construire la string d'env-vars (exclure certaines variables)
ENV_VARS=""
while IFS='=' read -r key value; do
    # Ignorer les commentaires et les lignes vides
    if [[ ! "$key" =~ ^# ]] && [ ! -z "$key" ]; then
        # Exclure les variables non-production
        if [[ ! "$key" =~ ^(DB_|REDIS_|CACHE_|SESSION_|MAIL_) ]]; then
            # Encoder les valeurs avec espaces
            value=$(echo "$value" | sed 's/"/\\"/g')
            ENV_VARS="$ENV_VARS,$key=$value"
        fi
    fi
done < .env

# Supprimer la première virgule
ENV_VARS=${ENV_VARS:1}

# Déployer
gcloud run deploy $SERVICE_NAME \
    --image=gcr.io/$PROJECT_ID/$SERVICE_NAME:$IMAGE_TAG \
    --region=$REGION \
    --platform=managed \
    --allow-unauthenticated \
    --memory=512Mi \
    --cpu=1 \
    --timeout=900 \
    --max-instances=100 \
    --min-instances=0 \
    --env-vars-file=.env.cloud

echo ""
echo -e "${GREEN}✅ Déploiement terminé !${NC}"
echo ""

# Obtenir l'URL du service
SERVICE_URL=$(gcloud run services describe $SERVICE_NAME --region=$REGION --format='value(status.url)')
echo -e "${GREEN}URL du service:${NC} $SERVICE_URL"

# Afficher les logs
echo ""
echo -e "${YELLOW}📊 Derniers logs :${NC}"
gcloud logging read "resource.type=cloud_run_revision AND resource.labels.service_name=$SERVICE_NAME" \
    --limit 10 --format='value(timestamp,severity,textPayload)' --sort-by='-timestamp'
