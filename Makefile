# ==========================================================================
# IOTCNT - Development Makefile
# ==========================================================================
#
# This Makefile provides convenient commands for development tasks
# Usage: make <command>

.PHONY: help install start stop restart status logs clean test build deploy backup

# Default target
help: ## Show this help message
	@echo "IOTCNT Development Commands:"
	@echo ""
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-15s\033[0m %s\n", $$1, $$2}'
	@echo ""

# Installation and Setup
install: ## Install and setup the complete system
	@echo "🚀 Installing IOTCNT system..."
	@cp .env.example .env || true
	@cp docker-compose.example.yml docker-compose.yml || true
	@cp docker/nginx/conf.d/app.example.conf docker/nginx/conf.d/app.conf || true
	@cp docker/mysql/my.example.cnf docker/mysql/my.cnf || true
	@cp docker/redis/redis.example.conf docker/redis/redis.conf || true
	@cp esp32_irrigation_controller/config.example.h esp32_irrigation_controller/config.h || true
	@echo "✅ Configuration files copied. Please edit them with your settings."

setup: ## Setup Laravel application (after containers are running)
	@echo "🔧 Setting up Laravel application..."
	docker-compose exec app composer install --optimize-autoloader
	docker-compose exec app php artisan key:generate --force
	docker-compose exec app php artisan migrate --force
	docker-compose exec app php artisan db:seed --force
	docker-compose exec app php artisan config:cache
	docker-compose exec app php artisan route:cache
	docker-compose exec app php artisan view:cache
	docker-compose exec app php artisan storage:link
	@echo "✅ Laravel setup completed!"

# Container Management
start: ## Start all containers
	@echo "🚀 Starting IOTCNT containers..."
	docker-compose up -d
	@echo "✅ Containers started!"

stop: ## Stop all containers
	@echo "🛑 Stopping IOTCNT containers..."
	docker-compose down
	@echo "✅ Containers stopped!"

restart: ## Restart all containers
	@echo "🔄 Restarting IOTCNT containers..."
	docker-compose restart
	@echo "✅ Containers restarted!"

build: ## Build all containers
	@echo "🏗️ Building IOTCNT containers..."
	docker-compose build --no-cache
	@echo "✅ Containers built!"

rebuild: ## Rebuild and restart containers
	@echo "🏗️ Rebuilding IOTCNT system..."
	docker-compose down
	docker-compose build --no-cache
	docker-compose up -d
	@echo "✅ System rebuilt!"

# Status and Monitoring
status: ## Show container status
	@echo "📊 IOTCNT System Status:"
	@docker-compose ps

health: ## Check system health
	@echo "🏥 IOTCNT Health Check:"
	@echo "Web Server:"
	@curl -s -o /dev/null -w "  HTTP Status: %{http_code}\n" http://localhost || echo "  ❌ Web server not responding"
	@echo "Database:"
	@docker-compose exec -T database mysqladmin ping -h localhost --silent && echo "  ✅ Database OK" || echo "  ❌ Database not responding"
	@echo "Redis:"
	@docker-compose exec -T redis redis-cli ping | grep -q PONG && echo "  ✅ Redis OK" || echo "  ❌ Redis not responding"

logs: ## Show all logs
	docker-compose logs -f

logs-app: ## Show Laravel application logs
	docker-compose logs -f app

logs-web: ## Show web server logs
	docker-compose logs -f webserver

logs-db: ## Show database logs
	docker-compose logs -f database

logs-redis: ## Show Redis logs
	docker-compose logs -f redis

# Development
dev: ## Start development environment with tools
	@echo "🛠️ Starting development environment..."
	docker-compose --profile dev up -d
	@echo "✅ Development environment started!"
	@echo "📱 phpMyAdmin: http://localhost:8080"
	@echo "📧 Mailpit: http://localhost:8025"
	@echo "🔧 Redis Commander: http://localhost:8081"

shell: ## Access Laravel container shell
	docker-compose exec app bash

shell-db: ## Access database shell
	docker-compose exec database mysql -u root -p

shell-redis: ## Access Redis shell
	docker-compose exec redis redis-cli

# Testing
test: ## Run all tests
	@echo "🧪 Running tests..."
	docker-compose exec app php artisan test

test-unit: ## Run unit tests only
	docker-compose exec app php artisan test --testsuite=Unit

test-feature: ## Run feature tests only
	docker-compose exec app php artisan test --testsuite=Feature

test-coverage: ## Run tests with coverage
	docker-compose exec app php artisan test --coverage

# Code Quality
lint: ## Run code linting
	docker-compose exec app ./vendor/bin/phpcs --standard=PSR12 app/

fix: ## Fix code style issues
	docker-compose exec app ./vendor/bin/phpcbf --standard=PSR12 app/

analyze: ## Run static analysis
	docker-compose exec app ./vendor/bin/phpstan analyse app/ --level=5

# Database
migrate: ## Run database migrations
	docker-compose exec app php artisan migrate

migrate-fresh: ## Fresh migration with seeding
	docker-compose exec app php artisan migrate:fresh --seed

seed: ## Run database seeders
	docker-compose exec app php artisan db:seed

# Cache Management
cache-clear: ## Clear all caches
	docker-compose exec app php artisan cache:clear
	docker-compose exec app php artisan config:clear
	docker-compose exec app php artisan route:clear
	docker-compose exec app php artisan view:clear

cache-build: ## Build all caches
	docker-compose exec app php artisan config:cache
	docker-compose exec app php artisan route:cache
	docker-compose exec app php artisan view:cache

# ESP32 Development
esp32-build: ## Build ESP32 firmware
	@echo "🔧 Building ESP32 firmware..."
	cd esp32_irrigation_controller && pio run

esp32-upload: ## Upload firmware to ESP32
	@echo "📤 Uploading firmware to ESP32..."
	cd esp32_irrigation_controller && pio run --target upload

esp32-monitor: ## Monitor ESP32 serial output
	@echo "📺 Monitoring ESP32 serial output..."
	cd esp32_irrigation_controller && pio device monitor

esp32-clean: ## Clean ESP32 build files
	cd esp32_irrigation_controller && pio run --target clean

# Backup and Restore
backup: ## Create database backup
	@echo "💾 Creating database backup..."
	@mkdir -p backups
	docker-compose exec -T database mysqldump -u root -p$(shell grep MYSQL_ROOT_PASSWORD docker-compose.yml | cut -d'=' -f2) iotcnt > backups/iotcnt_backup_$(shell date +%Y%m%d_%H%M%S).sql
	@echo "✅ Backup created in backups/ directory"

backup-auto: ## Run automatic backup script
	docker-compose run --rm backup /backup.sh

restore: ## Restore database from backup (requires BACKUP_FILE variable)
	@if [ -z "$(BACKUP_FILE)" ]; then echo "❌ Please specify BACKUP_FILE=path/to/backup.sql"; exit 1; fi
	@echo "🔄 Restoring database from $(BACKUP_FILE)..."
	docker-compose exec -T database mysql -u root -p$(shell grep MYSQL_ROOT_PASSWORD docker-compose.yml | cut -d'=' -f2) iotcnt < $(BACKUP_FILE)
	@echo "✅ Database restored!"

# Cleanup
clean: ## Clean up containers and volumes
	@echo "🧹 Cleaning up IOTCNT system..."
	docker-compose down -v
	docker system prune -f
	@echo "✅ Cleanup completed!"

clean-all: ## Clean everything including images
	@echo "🧹 Deep cleaning IOTCNT system..."
	docker-compose down -v --rmi all
	docker system prune -af
	@echo "✅ Deep cleanup completed!"

# Production
deploy: ## Deploy to production
	@echo "🚀 Deploying to production..."
	@if [ ! -f .env ]; then echo "❌ .env file not found!"; exit 1; fi
	@if grep -q "APP_ENV=local" .env; then echo "❌ Cannot deploy with APP_ENV=local!"; exit 1; fi
	docker-compose -f docker-compose.yml up -d --build
	$(MAKE) setup
	@echo "✅ Production deployment completed!"

# Monitoring
stats: ## Show container resource usage
	docker stats --no-stream

top: ## Show running processes in containers
	@echo "📊 Container Processes:"
	@for container in $$(docker-compose ps -q); do \
		echo "Container: $$(docker inspect --format='{{.Name}}' $$container)"; \
		docker exec $$container ps aux 2>/dev/null || echo "  Cannot access processes"; \
		echo ""; \
	done

# Utilities
open: ## Open web interface in browser
	@echo "🌐 Opening IOTCNT web interface..."
	@if command -v xdg-open > /dev/null; then xdg-open http://localhost; fi
	@if command -v open > /dev/null; then open http://localhost; fi
	@if command -v start > /dev/null; then start http://localhost; fi

version: ## Show version information
	@echo "IOTCNT System Information:"
	@echo "Docker: $$(docker --version)"
	@echo "Docker Compose: $$(docker-compose --version)"
	@echo "System: $$(uname -a)"

# Quick commands
up: start ## Alias for start
down: stop ## Alias for stop
ps: status ## Alias for status
