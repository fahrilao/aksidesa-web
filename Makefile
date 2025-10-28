.PHONY: help build up down restart logs shell clean

# Default target
help: ## Show this help message
	@echo "E-AKSIDESA Docker Management"
	@echo "============================"
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "\033[36m%-15s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)

# Build and start services
build: ## Build all Docker images
	docker-compose build

up: ## Start all services
	docker-compose up -d

down: ## Stop all services
	docker-compose down

restart: ## Restart all services
	docker-compose restart

# Logs
logs: ## Show logs for all services
	docker-compose logs -f

logs-laravel: ## Show Laravel logs
	docker-compose logs -f laravel

logs-frontend: ## Show Frontend logs
	docker-compose logs -f frontend

# Shell access
shell-laravel: ## Access Laravel container shell
	docker-compose exec laravel bash

shell-frontend: ## Access Frontend container shell
	docker-compose exec frontend sh

shell-mysql: ## Access MySQL shell
	docker-compose exec mysql mysql -u aksidesa_user -p aksidesa_db

# Laravel commands
migrate: ## Run Laravel migrations
	docker-compose exec laravel php artisan migrate

seed: ## Run Laravel seeders
	docker-compose exec laravel php artisan db:seed

fresh: ## Fresh migration with seed
	docker-compose exec laravel php artisan migrate:fresh --seed

cache-clear: ## Clear Laravel caches
	docker-compose exec laravel php artisan cache:clear
	docker-compose exec laravel php artisan config:clear
	docker-compose exec laravel php artisan route:clear
	docker-compose exec laravel php artisan view:clear

# Frontend commands
npm-install: ## Install frontend dependencies
	docker-compose exec frontend pnpm install

npm-build: ## Build frontend for production
	docker-compose exec frontend pnpm run build

# Development
dev: ## Start development environment
	@echo "Starting E-AKSIDESA development environment..."
	docker-compose up -d
	@echo "Services starting up..."
	@echo "Frontend: http://localhost"
	@echo "Backend API: http://localhost:8000"
	@echo "phpMyAdmin: http://localhost:8080"
	@echo "MailHog: http://localhost:8025"

# Cleanup
clean: ## Clean up containers and volumes
	docker-compose down -v
	docker system prune -f

clean-all: ## Clean up everything including images
	docker-compose down -v --rmi all
	docker system prune -a -f

# Status
status: ## Show service status
	docker-compose ps

# Health check
health: ## Check service health
	@echo "Checking service health..."
	@docker-compose ps --format "table {{.Name}}\t{{.Status}}\t{{.Ports}}"
