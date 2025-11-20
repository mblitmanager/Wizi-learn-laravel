# Script de déploiement sur Google Cloud Run (Windows PowerShell)
# Usage: .\deploy-to-cloudrun.ps1 -ProjectId "your-project-id" -ServiceName "wizi-learn-api" -Region "europe-west1"

param(
    [Parameter(Mandatory=$true)]
    [string]$ProjectId,
    
    [Parameter(Mandatory=$false)]
    [string]$ServiceName = "wizi-learn-api",
    
    [Parameter(Mandatory=$false)]
    [string]$Region = "europe-west1",
    
    [Parameter(Mandatory=$false)]
    [string]$ImageTag = "latest"
)

$ErrorActionPreference = "Stop"

# Couleurs
function Write-Success {
    param([string]$Message)
    Write-Host "✅ $Message" -ForegroundColor Green
}

function Write-Info {
    param([string]$Message)
    Write-Host "ℹ️  $Message" -ForegroundColor Cyan
}

function Write-Warning {
    param([string]$Message)
    Write-Host "⚠️  $Message" -ForegroundColor Yellow
}

function Write-Error {
    param([string]$Message)
    Write-Host "❌ $Message" -ForegroundColor Red
}

# En-tête
Write-Host ""
Write-Host "═════════════════════════════════════════════════════" -ForegroundColor Cyan
Write-Host "  🚀 DÉPLOIEMENT SUR GOOGLE CLOUD RUN" -ForegroundColor Cyan
Write-Host "═════════════════════════════════════════════════════" -ForegroundColor Cyan
Write-Host ""
Write-Info "Project ID : $ProjectId"
Write-Info "Service : $ServiceName"
Write-Info "Region : $Region"
Write-Info "Image Tag : $ImageTag"
Write-Host ""

try {
    # Configurer gcloud
    Write-Warning "Configuration de gcloud..."
    & gcloud config set project $ProjectId
    Write-Success "Projet configuré"
    
    # Vérifier l'authentification
    Write-Warning "Vérification de l'authentification..."
    & gcloud auth list
    
    # Activer les APIs
    Write-Warning "Activation des APIs Google Cloud..."
    & gcloud services enable run.googleapis.com
    & gcloud services enable cloudbuild.googleapis.com
    & gcloud services enable container.googleapis.com
    & gcloud services enable artifactregistry.googleapis.com
    Write-Success "APIs activées"
    
    # Builder l'image Docker
    Write-Warning "Construction de l'image Docker..."
    $ImageUri = "gcr.io/$ProjectId/$ServiceName`:$ImageTag"
    & docker build -t $ImageUri .
    Write-Success "Image construite : $ImageUri"
    
    # Obtenir le git commit short hash
    $CommitHash = & git rev-parse --short HEAD
    $ImageUriWithCommit = "gcr.io/$ProjectId/$ServiceName`:$CommitHash"
    
    if ($LASTEXITCODE -eq 0) {
        & docker build -t $ImageUriWithCommit .
        Write-Success "Image construite avec tag commit : $ImageUriWithCommit"
    }
    
    # Push l'image vers GCR
    Write-Warning "Push de l'image vers Google Container Registry..."
    & docker push $ImageUri
    Write-Success "Image pushée : $ImageUri"
    
    if ($LASTEXITCODE -eq 0 -and $CommitHash) {
        & docker push $ImageUriWithCommit
        Write-Success "Image pushée avec commit : $ImageUriWithCommit"
    }
    
    # Vérifier le fichier .env.cloud
    if (!(Test-Path ".env.cloud")) {
        Write-Error ".env.cloud non trouvé. Créez ce fichier avec les variables de production."
        Write-Info "Vous pouvez copier et adapter .env.docker.example"
        exit 1
    }
    
    # Déployer sur Cloud Run
    Write-Warning "Déploiement sur Cloud Run..."
    & gcloud run deploy $ServiceName `
        --image=$ImageUri `
        --region=$Region `
        --platform=managed `
        --allow-unauthenticated `
        --memory=512Mi `
        --cpu=1 `
        --timeout=900 `
        --max-instances=100 `
        --min-instances=0 `
        --env-vars-file=.env.cloud
    
    Write-Success "Déploiement terminé !"
    
    # Obtenir l'URL du service
    Write-Host ""
    Write-Warning "Récupération de l'URL du service..."
    $ServiceUrl = & gcloud run services describe $ServiceName --region=$Region --format='value(status.url)'
    Write-Success "URL : $ServiceUrl"
    
    # Afficher les logs
    Write-Host ""
    Write-Warning "Affichage des derniers logs..."
    & gcloud logging read "resource.type=cloud_run_revision AND resource.labels.service_name=$ServiceName" `
        --limit=10 `
        --format="table(timestamp,severity,textPayload)" `
        --sort-by='-timestamp'
    
    Write-Host ""
    Write-Success "✨ Déploiement réussi !"
    Write-Info "Accédez à votre service sur : $ServiceUrl"
    
}
catch {
    Write-Error "Une erreur s'est produite : $_"
    exit 1
}
