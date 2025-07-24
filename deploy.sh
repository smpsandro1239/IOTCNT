#!/bin/bash

# IOTCNT Deployment Script
# This script helps deploy the IOTCNT irrigation system

set -e

echo "🌱 IOTCNT Deployment Script"
echo "=========================="

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

print_step() {
    echo -e "${BLUE}[STEP]${NC} $1"
}

# Check if Docker is installed
check_docker() {
    if ! command -v docker &> /dev/null; then
        print_error "Docker is not installed. Please install Docker first."
        exit 1
    fi

    if ! command -v docker-compose &> /dev/null; then
        print_error "Docker Compose is not installed. Please install Docker Compose first."
        exit 1
    fi

    print_status "Docker and Docker Compose are installed."
}

# Create environment file
setup_environment() {
    print_step "Setting up environment configuration..."

    if [ ! -f .env ]; then
        if [ -f .env.example ]; then
            cp .env.example .env
            print_status "Created .env file from .env.example"
        else
            print_warning ".env.example not found. Creating basic .env file..."
            cat > .env << EOF
APP_NAME="IOTCNT"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=http://localhost

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=database
DB_PORT=3306
DB_DATABASE=iotcnt
DB_USERNAME=iotcnt_user
DB_PASSWORD=secure_password_here

BROADCAST_DRIVER=log
CACHE_DRIVER=redis
FILESYSTEM_DISK=local
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
SESSION_LIFETIME=120

REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="\${APP_NAME}"

# Telegram Bot Configuration
TELEGRAM_BOT_TOKEN=your_telegram_bot_token_here
TELEGRAM_BOT_USERNAME=your_bot_username

# ESP32 Configuration
ESP32_API_TOKEN=your_esp32_api_token_here
EOF
        fi

        print_warning "Please edit the .env file with your specific configuration before continuing."
        print_warning "Important: Set your database passwords, Telegram bot token, and ESP32 API token."

        read -p "Press Enter to continue after editing .env file..."
    else
        print_status ".env file already exists."
    fi
}

# Generate application key
generate_app_key() {
    print_step "Generating application key..."

    if grep -q "APP_KEY=$" .env; then
        # Generate a random 32-character key
        APP_KEY=$(openssl rand -base64 32)
        sed -i "s/APP_KEY=$/APP_KEY=base64:$APP_KEY/" .env
        print_status "Application key generated."
    else
        print_status "Application key already set."
    fi
}

# Build and start containers
start_containers() {
    print_step "Building and starting Docker containers..."

    docker-compose down --remove-orphans
    docker-compose build --no-cache
    docker-compose up -d

    print_status "Containers started successfully."

    # Wait for database to be ready
    print_step "Waiting for database to be ready..."
    sleep 30
}

# Run Laravel setup
setup_laravel() {
    print_step "Setting up Laravel application..."

    # Install dependencies
    docker-compose exec app composer install --no-dev --optimize-autoloader

    # Generate application key if not set
    docker-compose exec app php artisan key:generate --force

    # Run migrations
    docker-compose exec app php artisan migrate --force

    # Seed database (if seeders exist)
    if docker-compose exec app php artisan db:seed --class=DatabaseSeeder --dry-run &> /dev/null; then
        docker-compose exec app php artisan db:seed --force
        print_status "Database seeded."
    fi

    # Clear and cache config
    docker-compose exec app php artisan config:cache
    docker-compose exec app php artisan route:cache
    docker-compose exec app php artisan view:cache

    # Create storage link
    docker-compose exec app php artisan storage:link

    # Set permissions
    docker-compose exec app chown -R www:www /var/www/storage
    docker-compose exec app chown -R www:www /var/www/bootstrap/cache

    print_status "Laravel application setup completed."
}

# Create admin user
create_admin_user() {
    print_step "Creating admin user..."

    read -p "Enter admin email: " ADMIN_EMAIL
    read -s -p "Enter admin password: " ADMIN_PASSWORD
    echo

    docker-compose exec app php artisan tinker --execute="
    \$user = App\Models\User::firstOrCreate(
        ['email' => '$ADMIN_EMAIL'],
        [
            'name' => 'Administrator',
            'password' => Hash::make('$ADMIN_PASSWORD'),
            'role' => 'admin'
        ]
    );
    echo 'Admin user created: ' . \$user->email;
    "

    print_status "Admin user created successfully."
}

# Setup Telegram webhook
setup_telegram() {
    print_step "Setting up Telegram webhook..."

    read -p "Do you want to set up Telegram webhook now? (y/n): " -n 1 -r
    echo

    if [[ $REPLY =~ ^[Yy]$ ]]; then
        read -p "Enter your domain (e.g., https://yourdomain.com): " DOMAIN

        # This would typically be done through the web interface or API
        print_status "Please visit $DOMAIN/telegram/set-webhook to configure the Telegram webhook."
    fi
}

# Show final information
show_final_info() {
    print_step "Deployment completed!"
    echo
    print_status "Your IOTCNT system is now running!"
    echo
    echo "📱 Web Interface: http://localhost"
    echo "🗄️  Database: localhost:3306"
    echo "🔧 Redis: localhost:6379"
    echo
    echo "📋 Next steps:"
    echo "1. Configure your ESP32 device with the API endpoint and token"
    echo "2. Set up your Telegram bot webhook"
    echo "3. Create irrigation schedules through the web interface"
    echo "4. Add and configure your irrigation valves"
    echo
    print_warning "Remember to:"
    echo "- Change default passwords in production"
    echo "- Set up SSL/TLS certificates for HTTPS"
    echo "- Configure firewall rules"
    echo "- Set up regular backups"
    echo
    print_status "Happy irrigating! 🌱💧"
}

# Main deployment flow
main() {
    print_status "Starting IOTCNT deployment..."

    check_docker
    setup_environment
    generate_app_key
    start_containers
    setup_laravel
    create_admin_user
    setup_telegram
    show_final_info
}

# Run main function
main "$@"
