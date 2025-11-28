# scripts/start-docker.ps1
# PowerShell script to prepare .env, start docker compose and run migrations
param(
    [switch]$Force
)

$root = Split-Path -Parent $MyInvocation.MyCommand.Definition
$scriptDir = Split-Path -Parent $MyInvocation.MyCommand.Definition
# Repo root is parent of the scripts folder
$rootResolved = Resolve-Path -Path (Join-Path $scriptDir '..')
$root = $rootResolved.Path
Set-Location $root

# Backup existing .env if exists and not forced
if (Test-Path .env) {
    if (-not $Force) {
        $timestamp = Get-Date -Format "yyyyMMddHHmmss"
        Write-Host "Found existing .env -> backing up to .env.bak.$timestamp"
        Copy-Item -Path .env -Destination ".env.bak.$timestamp" -Force
    } else {
        Write-Host "Overwriting existing .env (force)"
    }
}

# Copy .env.docker -> .env
if (-not (Test-Path .env.docker)) {
    Write-Error ".env.docker not found in $PWD. Please create it from .env.docker.example"
    exit 1
}
Copy-Item -Path .env.docker -Destination .env -Force
Write-Host "Copied .env.docker -> .env"

# Start docker compose (build if needed)
Write-Host "Starting docker compose..."
$composeCmd = "docker compose up -d --build"
Write-Host $composeCmd
& docker compose up -d --build

# Wait for MySQL container to be healthy (wizi-mysql)
$mysqlContainer = "wizi-mysql"
$maxAttempts = 60
$attempt = 0
$healthy = $false
Write-Host "Waiting for MySQL container to become healthy (timeout $($maxAttempts*5)s)"
while ($attempt -lt $maxAttempts) {
    try {
        $status = docker inspect --format '{{.State.Health.Status}}' $mysqlContainer 2>$null
        if ($status -eq 'healthy') {
            $healthy = $true
            break
        }
    } catch {
        # ignore and continue
    }
    Start-Sleep -Seconds 5
    $attempt++
}

if (-not $healthy) {
    Write-Warning "MySQL container did not report healthy state. Proceeding anyway."
} else {
    Write-Host "MySQL healthy"
}

# Run migrations
Write-Host "Running migrations inside app container..."
& docker compose exec app php artisan migrate --force

# Seed if RUN_SEEDS is set to true in env
$runSeeds = (Get-Content .env | Select-String -Pattern '^RUN_SEEDS=').ToString().Split('=')[-1]
if ($runSeeds -eq 'true') {
    Write-Host "RUN_SEEDS=true -> running seeders"
    & docker compose exec app php artisan db:seed
}

Write-Host "Startup complete. App should be available at http://localhost:8000"
Write-Host "phpMyAdmin: http://localhost:8080 (user: wizi or root)"

exit 0
