# E-AKSIDESA Docker Setup

This repository includes a complete Docker setup for running the E-AKSIDESA application stack.

## 🚀 Quick Start

### Prerequisites
- Docker Engine 20.10+
- Docker Compose 2.0+
- At least 4GB RAM available for containers

### 1. Clone and Setup
```bash
git clone <repository-url>
cd AKSIDESA-PERMINTAAN-SURAT
```

### 2. Environment Configuration
```bash
# Copy Laravel environment file
cp .env.example .env

# Update .env with Docker settings (optional - defaults work)
DB_HOST=mysql
DB_DATABASE=aksidesa_db
DB_USERNAME=aksidesa_user
DB_PASSWORD=aksidesa_password
REDIS_HOST=redis
```

### 3. Start the Application
```bash
# Build and start all services
docker-compose up -d

# View logs
docker-compose logs -f
```

### 4. Access the Application
- **Frontend (React)**: http://localhost
- **Backend API**: http://localhost:8000
- **phpMyAdmin**: http://localhost:8080
- **MailHog**: http://localhost:8025

## 📦 Services Overview

### Core Services
| Service | Port | Description |
|---------|------|-------------|
| `nginx` | 80, 443 | Reverse proxy & load balancer |
| `laravel` | 8000 | Laravel API backend |
| `frontend` | 3000 | React frontend application |
| `mysql` | 3306 | MySQL 8.0 database |
| `redis` | 6379 | Redis cache & sessions |

### Development Services
| Service | Port | Description |
|---------|------|-------------|
| `phpmyadmin` | 8080 | Database management |
| `mailhog` | 8025, 1025 | Email testing |

## 🛠️ Development Commands

### Application Management
```bash
# Start services
docker-compose up -d

# Stop services
docker-compose down

# Restart specific service
docker-compose restart laravel

# View logs
docker-compose logs -f laravel
docker-compose logs -f frontend

# Execute commands in containers
docker-compose exec laravel php artisan migrate
docker-compose exec laravel php artisan db:seed
docker-compose exec frontend pnpm install
```

### Database Management
```bash
# Run migrations
docker-compose exec laravel php artisan migrate

# Seed database
docker-compose exec laravel php artisan db:seed

# Reset database
docker-compose exec laravel php artisan migrate:fresh --seed

# Access MySQL CLI
docker-compose exec mysql mysql -u aksidesa_user -p aksidesa_db
```

### Laravel Commands
```bash
# Clear caches
docker-compose exec laravel php artisan cache:clear
docker-compose exec laravel php artisan config:clear
docker-compose exec laravel php artisan route:clear
docker-compose exec laravel php artisan view:clear

# Generate app key
docker-compose exec laravel php artisan key:generate

# Create storage link
docker-compose exec laravel php artisan storage:link

# Run queue workers
docker-compose exec laravel php artisan queue:work
```

### Frontend Commands
```bash
# Install dependencies
docker-compose exec frontend pnpm install

# Build for production
docker-compose exec frontend pnpm run build

# Run development server
docker-compose exec frontend pnpm run dev
```

## 🔧 Configuration

### Environment Variables
Create a `.env` file in the root directory with these key settings:

```env
# Application
APP_NAME="E-AKSIDESA"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

# Database
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=aksidesa_db
DB_USERNAME=aksidesa_user
DB_PASSWORD=aksidesa_password

# Cache & Sessions
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Redis
REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379

# Mail (using MailHog)
MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
```

### Frontend Environment
The frontend automatically uses these environment variables:
```env
VITE_API_BASE_URL=http://localhost:8000
VITE_APP_NAME="E-AKSIDESA"
```

## 📁 Docker Structure

```
docker/
├── laravel/
│   ├── Dockerfile          # Laravel container
│   ├── nginx.conf          # Internal nginx config
│   ├── supervisord.conf    # Process management
│   └── start.sh           # Startup script
├── frontend/
│   └── Dockerfile          # React container
├── nginx/
│   ├── nginx.conf          # Main nginx config
│   └── sites/
│       └── default.conf    # Site configuration
└── mysql/
    └── init/
        └── 01-create-database.sql
```

## 🔍 Troubleshooting

### Common Issues

#### Services won't start
```bash
# Check service status
docker-compose ps

# View service logs
docker-compose logs <service-name>

# Restart problematic service
docker-compose restart <service-name>
```

#### Database connection issues
```bash
# Ensure MySQL is healthy
docker-compose exec mysql mysqladmin ping

# Check Laravel database connection
docker-compose exec laravel php artisan migrate:status
```

#### Permission issues
```bash
# Fix Laravel permissions
docker-compose exec laravel chown -R www-data:www-data storage bootstrap/cache
docker-compose exec laravel chmod -R 775 storage bootstrap/cache
```

#### Frontend build issues
```bash
# Clear node modules and reinstall
docker-compose exec frontend rm -rf node_modules
docker-compose exec frontend pnpm install
docker-compose exec frontend pnpm run build
```

### Performance Optimization

#### For Development
```bash
# Use bind mounts for faster file sync
# Already configured in docker-compose.yml
```

#### For Production
```bash
# Build optimized images
docker-compose -f docker-compose.yml -f docker-compose.prod.yml up -d

# Enable PHP OPcache
# Add to Laravel Dockerfile: RUN docker-php-ext-install opcache
```

## 🚀 Production Deployment

### Build Production Images
```bash
# Build all images
docker-compose build

# Tag for registry
docker tag aksidesa_laravel your-registry/aksidesa-laravel:latest
docker tag aksidesa_frontend your-registry/aksidesa-frontend:latest

# Push to registry
docker push your-registry/aksidesa-laravel:latest
docker push your-registry/aksidesa-frontend:latest
```

### Environment Setup
1. Copy `docker-compose.yml` to production server
2. Create production `.env` file
3. Update database credentials and API URLs
4. Configure SSL certificates for nginx
5. Set up proper backup strategies

## 📊 Monitoring

### Health Checks
All services include health checks:
```bash
# Check service health
docker-compose ps
```

### Logs
```bash
# Application logs
docker-compose logs -f laravel frontend

# Database logs
docker-compose logs -f mysql

# Web server logs
docker-compose logs -f nginx
```

## 🔐 Security Notes

- Change default passwords in production
- Use environment-specific `.env` files
- Configure proper firewall rules
- Enable SSL/TLS for production
- Regular security updates for base images
- Implement proper backup strategies

## 📞 Support

For issues related to:
- **Docker setup**: Check this README and troubleshooting section
- **Laravel backend**: Check Laravel documentation
- **React frontend**: Check React/Vite documentation
- **Database**: Check MySQL documentation

---

**Happy Coding! 🎉**
