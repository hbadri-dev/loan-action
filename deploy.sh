#!/bin/bash

# Loan Auction System Deployment Script
# Usage: ./deploy.sh [dev|prod]

set -e

ENVIRONMENT=${1:-dev}

echo "🚀 Starting deployment for $ENVIRONMENT environment..."

# Check if Docker is running
if ! docker info > /dev/null 2>&1; then
    echo "❌ Docker is not running. Please start Docker first."
    exit 1
fi

# Stop existing containers
echo "🛑 Stopping existing containers..."
docker-compose -f docker-compose.yml -f docker-compose.$ENVIRONMENT.yml down

# Build images
echo "🔨 Building Docker images..."
docker-compose -f docker-compose.yml -f docker-compose.$ENVIRONMENT.yml build

# Start containers
echo "🚀 Starting containers..."
docker-compose -f docker-compose.yml -f docker-compose.$ENVIRONMENT.yml up -d

# Wait for MySQL to be ready
echo "⏳ Waiting for MySQL to be ready..."
sleep 30

# Run migrations and seeders
echo "📊 Running migrations and seeders..."
docker-compose -f docker-compose.yml -f docker-compose.$ENVIRONMENT.yml exec app php artisan migrate --force
docker-compose -f docker-compose.yml -f docker-compose.$ENVIRONMENT.yml exec app php artisan db:seed --force

# Build assets
echo "🎨 Building assets..."
docker-compose -f docker-compose.yml -f docker-compose.$ENVIRONMENT.yml exec app npm run build

# Clear caches
echo "🧹 Clearing caches..."
docker-compose -f docker-compose.yml -f docker-compose.$ENVIRONMENT.yml exec app php artisan cache:clear
docker-compose -f docker-compose.yml -f docker-compose.$ENVIRONMENT.yml exec app php artisan config:clear
docker-compose -f docker-compose.yml -f docker-compose.$ENVIRONMENT.yml exec app php artisan route:clear
docker-compose -f docker-compose.yml -f docker-compose.$ENVIRONMENT.yml exec app php artisan view:clear

echo "✅ Deployment completed successfully!"
echo "🌐 Application is available at:"
if [ "$ENVIRONMENT" = "prod" ]; then
    echo "   https://localhost"
else
    echo "   http://localhost:8080"
fi

echo ""
echo "👤 Default users:"
echo "   Admin: admin@loanauction.com / password"
echo "   Seller: seller@loanauction.com / password"
echo "   Buyer: buyer@loanauction.com / password"
