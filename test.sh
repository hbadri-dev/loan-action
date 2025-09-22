#!/bin/bash

# Loan Auction System Test Script
# Usage: ./test.sh

set -e

echo "🧪 Starting test suite..."

# Check if Docker is running
if ! docker info > /dev/null 2>&1; then
    echo "❌ Docker is not running. Please start Docker first."
    exit 1
fi

# Start test containers
echo "🚀 Starting test containers..."
docker-compose -f docker-compose.yml -f docker-compose.test.yml up -d

# Wait for MySQL to be ready
echo "⏳ Waiting for MySQL to be ready..."
sleep 30

# Run migrations for test database
echo "📊 Setting up test database..."
docker-compose -f docker-compose.yml -f docker-compose.test.yml exec app php artisan migrate --force

# Run tests
echo "🧪 Running tests..."
docker-compose -f docker-compose.yml -f docker-compose.test.yml exec app php artisan test

# Run code style check
echo "🎨 Running code style check..."
docker-compose -f docker-compose.yml -f docker-compose.test.yml exec app ./vendor/bin/pint --test

# Stop test containers
echo "🛑 Stopping test containers..."
docker-compose -f docker-compose.yml -f docker-compose.test.yml down

echo "✅ Test suite completed successfully!"
