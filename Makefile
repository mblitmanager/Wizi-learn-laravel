.PHONY: help build up down logs migrate seed shell artisan

# Variables
DOCKER_COMPOSE = docker-compose
APP_CONTAINER = app
MYSQL_CONTAINER = mysql
REDIS_CONTAINER = redis

# Colors
BLUE = \033[0;34m
GREEN = \033[0;32m
RED = \033[0;31m
NC = \033[0m

help:
	@echo "$(BLUE)Wizi Learn - Docker Commands$(NC)"
	@echo ""
	@echo "$(GREEN)Setup & Installation:$(NC)"
	@echo "  make install         - Build and start containers"
	@echo "  make build           - Build Docker images"
	@echo ""
	@echo "$(GREEN)Container Management:$(NC)"
	@echo "  make up              - Start containers"
	@echo "  make down            - Stop containers"
	@echo "  make restart         - Restart containers"
	@echo "  make logs            - View application logs"
	@echo "  make ps              - Show container status"
	@echo ""
	@echo "$(GREEN)Database:$(NC)"
	@echo "  make migrate         - Run database migrations"
	@echo "  make seed            - Seed the database"
	@echo "  make db-fresh        - Refresh database (migrate + seed)"
	@echo "  make db-shell        - Access MySQL shell"
	@echo ""
	@echo "$(GREEN)Cache & Optimization:$(NC)"
	@echo "  make cache-clear     - Clear all application caches"
	@echo "  make cache-optimize  - Cache for production"
	@echo ""
	@echo "$(GREEN)Development:$(NC)"
	@echo "  make shell           - Access app container shell"
	@echo "  make artisan         - Run artisan command (make artisan cmd='command')"
	@echo "  make tinker          - Start Laravel Tinker REPL"
	@echo "  make test            - Run tests"
	@echo ""
	@echo "$(GREEN)Cloud Deployment:$(NC)"
	@echo "  make cloud-deploy    - Deploy to Google Cloud Run"
	@echo "  make cloud-logs      - View Cloud Run logs"
	@echo ""

# Setup & Installation
install: build up migrate
	@echo "$(GREEN)✅ Installation completed!$(NC)"

build:
	@echo "$(BLUE)Building Docker images...$(NC)"
	$(DOCKER_COMPOSE) build

# Container Management
up:
	@echo "$(BLUE)Starting containers...$(NC)"
	$(DOCKER_COMPOSE) up -d
	@echo "$(GREEN)✅ Containers started!$(NC)"
	@echo "📍 Application: http://localhost:8000"
	@echo "📍 MailHog: http://localhost:8025"

down:
	@echo "$(BLUE)Stopping containers...$(NC)"
	$(DOCKER_COMPOSE) down

restart:
	@echo "$(BLUE)Restarting containers...$(NC)"
	$(DOCKER_COMPOSE) restart
	@echo "$(GREEN)✅ Containers restarted!$(NC)"

logs:
	$(DOCKER_COMPOSE) logs -f --tail=100 app

ps:
	$(DOCKER_COMPOSE) ps

# Database Commands
migrate:
	@echo "$(BLUE)Running migrations...$(NC)"
	$(DOCKER_COMPOSE) exec app php artisan migrate --force
	@echo "$(GREEN)✅ Migrations completed!$(NC)"

seed:
	@echo "$(BLUE)Seeding database...$(NC)"
	$(DOCKER_COMPOSE) exec app php artisan db:seed
	@echo "$(GREEN)✅ Database seeded!$(NC)"

db-fresh:
	@echo "$(BLUE)Refreshing database...$(NC)"
	$(DOCKER_COMPOSE) exec app php artisan migrate:refresh --seed --force
	@echo "$(GREEN)✅ Database refreshed!$(NC)"

db-shell:
	$(DOCKER_COMPOSE) exec mysql mysql -u wizi -p

# Cache & Optimization
cache-clear:
	@echo "$(BLUE)Clearing caches...$(NC)"
	$(DOCKER_COMPOSE) exec app php artisan cache:clear
	$(DOCKER_COMPOSE) exec app php artisan route:clear
	$(DOCKER_COMPOSE) exec app php artisan config:clear
	$(DOCKER_COMPOSE) exec app php artisan view:clear
	@echo "$(GREEN)✅ Caches cleared!$(NC)"

cache-optimize:
	@echo "$(BLUE)Optimizing for production...$(NC)"
	$(DOCKER_COMPOSE) exec app php artisan config:cache
	$(DOCKER_COMPOSE) exec app php artisan route:cache
	$(DOCKER_COMPOSE) exec app php artisan view:cache
	@echo "$(GREEN)✅ Application optimized!$(NC)"

# Development
shell:
	$(DOCKER_COMPOSE) exec app bash

artisan:
	$(DOCKER_COMPOSE) exec app php artisan $(cmd)

tinker:
	$(DOCKER_COMPOSE) exec app php artisan tinker

test:
	@echo "$(BLUE)Running tests...$(NC)"
	$(DOCKER_COMPOSE) exec app php artisan test
	@echo "$(GREEN)✅ Tests completed!$(NC)"

# Redis
redis-shell:
	$(DOCKER_COMPOSE) exec redis redis-cli

redis-monitor:
	$(DOCKER_COMPOSE) exec redis redis-cli monitor

# Cloud Deployment
cloud-deploy:
	@echo "$(BLUE)Deploying to Google Cloud Run...$(NC)"
	./deploy-to-cloudrun.ps1
	@echo "$(GREEN)✅ Deployment completed!$(NC)"

cloud-logs:
	@echo "$(BLUE)Fetching Cloud Run logs...$(NC)"
	gcloud logging read "resource.type=cloud_run_revision" --limit 50 --format json

# Clean up
clean:
	@echo "$(BLUE)Cleaning up...$(NC)"
	$(DOCKER_COMPOSE) down
	docker system prune -f
	@echo "$(GREEN)✅ Cleanup completed!$(NC)"

clean-volumes:
	@echo "$(RED)Removing containers and volumes...$(NC)"
	$(DOCKER_COMPOSE) down -v
	@echo "$(GREEN)✅ All data cleared!$(NC)"

# Status
status:
	@echo "$(BLUE)Container Status:$(NC)"
	@$(DOCKER_COMPOSE) ps
	@echo ""
	@echo "$(BLUE)Docker Images:$(NC)"
	@docker images | grep wizi
	@echo ""
	@echo "$(BLUE)Disk Usage:$(NC)"
	@docker system df
