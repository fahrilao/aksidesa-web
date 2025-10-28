#!/bin/bash

echo "🚀 Starting E-AKSIDESA Application Stack..."
echo "=========================================="

# Check if Docker is running
if ! docker info > /dev/null 2>&1; then
    echo "❌ Docker is not running. Please start Docker first."
    exit 1
fi

# Check if docker-compose is available
if ! command -v docker &> /dev/null; then
    echo "❌ Docker is not installed. Please install Docker first."
    exit 1
fi

echo "📦 Building and starting containers..."
docker compose up -d --build

echo ""
echo "⏳ Waiting for services to be ready..."
sleep 10

# Check service health
echo "🔍 Checking service health..."

# Check MySQL
if docker compose exec mysql mysqladmin ping -h localhost --silent; then
    echo "✅ MySQL: Ready"
else
    echo "❌ MySQL: Not ready"
fi

# Check Redis
if docker compose exec redis redis-cli ping > /dev/null 2>&1; then
    echo "✅ Redis: Ready"
else
    echo "❌ Redis: Not ready"
fi

# Check Laravel API
if curl -f http://localhost:8000/api/health > /dev/null 2>&1; then
    echo "✅ Laravel API: Ready"
else
    echo "❌ Laravel API: Not ready"
fi

# Check Frontend
if curl -f http://localhost:3000 > /dev/null 2>&1; then
    echo "✅ React Frontend: Ready"
else
    echo "❌ React Frontend: Not ready"
fi

echo ""
echo "🎉 E-AKSIDESA Application Stack Started!"
echo "========================================"
echo ""
echo "📱 Access Points:"
echo "  • Frontend (React):     http://localhost"
echo "  • Backend API:          http://localhost:8000"
echo "  • Database (phpMyAdmin): http://localhost:8080"
echo ""
echo "🛠️  Management Commands:"
echo "  • View logs:            docker compose logs -f"
echo "  • Stop services:        docker compose down"
echo "  • Restart services:     docker compose restart"
echo "  • View status:          docker compose ps"
echo ""
echo "📚 For more commands, see: make help"
echo ""
