#!/bin/bash

echo "🛑 Stopping E-AKSIDESA Application Stack..."
echo "==========================================="

# Stop all services
docker compose down

echo ""
echo "✅ All services stopped successfully!"
echo ""
echo "💡 To start again, run: ./start.sh or make dev"
echo ""
