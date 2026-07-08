# Production Deployment Guide - Single Container

This guide covers deploying the KTL Booking System as a single, production-ready Docker container.

## ✅ What's Included

The single container includes:
- **Nginx** - Web server (port 80)
- **PHP-FPM 8.2** - Application runtime
- **Scheduler Daemon** - Background task processor
- **Supervisor** - Process manager for all services
- **OPcache** - Production PHP optimization
- **Health checks** - Automatic monitoring

## 🚀 Quick Deploy

### Build the Image

```bash
docker build -f Dockerfile.production -t ktl-booking:latest .
```

### Run with External MySQL

```bash
docker run -d \
  --name ktl-booking \
  -p 80:80 \
  -e APP_KEY=base64:YOUR_APP_KEY_HERE \
  -e DB_HOST=your-mysql-host \
  -e DB_PORT=3306 \
  -e DB_DATABASE=ktl_booking \
  -e DB_USERNAME=your_user \
  -e DB_PASSWORD=your_password \
  --restart unless-stopped \
  ktl-booking:latest
```

### Run with Docker Compose (includes MySQL)

Create `docker-compose.production.yml`:

```yaml
version: '3.8'

services:
  app:
    build:
      context: .
      dockerfile: Dockerfile.production
    ports:
      - "80:80"
    environment:
      APP_NAME: "KTL Booking"
      APP_ENV: production
      APP_DEBUG: false
      APP_KEY: ${APP_KEY}
      DB_CONNECTION: mysql
      DB_HOST: mysql
      DB_PORT: 3306
      DB_DATABASE: ktl_booking
      DB_USERNAME: ktl_user
      DB_PASSWORD: ${DB_PASSWORD}
    depends_on:
      - mysql
    restart: unless-stopped
    healthcheck:
      test: ["CMD", "curl", "-f", "http://localhost/health"]
      interval: 30s
      timeout: 3s
      retries: 3

  mysql:
    image: mysql:8.0
    environment:
      MYSQL_ROOT_PASSWORD: ${MYSQL_ROOT_PASSWORD}
      MYSQL_DATABASE: ktl_booking
      MYSQL_USER: ktl_user
      MYSQL_PASSWORD: ${DB_PASSWORD}
    volumes:
      - mysql-data:/var/lib/mysql
    restart: unless-stopped
    healthcheck:
      test: ["CMD", "mysqladmin", "ping", "-h", "localhost"]
      interval: 10s
      timeout: 5s
      retries: 5

volumes:
  mysql-data:
```

Then run:

```bash
docker-compose -f docker-compose.production.yml up -d
```

## 🌐 Platform-Specific Deployment

### Railway

1. Create `railway.json`:
```json
{
  "$schema": "https://railway.app/railway.schema.json",
  "build": {
    "builder": "DOCKERFILE",
    "dockerfilePath": "Dockerfile.production"
  },
  "deploy": {
    "startCommand": "supervisord -c /etc/supervisor/conf.d/supervisord.conf",
    "healthcheckPath": "/health",
    "healthcheckTimeout": 300
  }
}
```

2. Add MySQL database service in Railway dashboard
3. Deploy from GitHub repo

### Fly.io

1. Create `fly.toml`:
```toml
app = "ktl-booking"
primary_region = "lhr"

[build]
  dockerfile = "Dockerfile.production"

[env]
  APP_ENV = "production"
  APP_DEBUG = "false"

[[services]]
  http_checks = []
  internal_port = 80
  processes = ["app"]
  protocol = "tcp"
  script_checks = []

  [[services.ports]]
    force_https = true
    handlers = ["http"]
    port = 80

  [[services.ports]]
    handlers = ["tls", "http"]
    port = 443

  [[services.tcp_checks]]
    grace_period = "30s"
    interval = "15s"
    restart_limit = 0
    timeout = "2s"
```

2. Create MySQL database: `fly postgres create`
3. Deploy: `fly launch`

### DigitalOcean App Platform

1. Fork repo to GitHub
2. Create new App
3. Select GitHub repo
4. Choose "Dockerfile" as build method
5. Set Dockerfile path: `Dockerfile.production`
6. Add MySQL managed database
7. Set environment variables
8. Deploy

### Heroku

Not recommended (Heroku doesn't support Dockerfile with multiple processes well). Use Railway or Fly.io instead.

## 🔧 Environment Variables

Required:
- `APP_KEY` - Laravel app key (generate with `php artisan key:generate`)
- `DB_HOST` - MySQL host
- `DB_DATABASE` - Database name
- `DB_USERNAME` - Database user
- `DB_PASSWORD` - Database password

Optional:
- `APP_ENV` - Environment (default: production)
- `APP_DEBUG` - Debug mode (default: false)
- `APP_URL` - Application URL
- `DB_PORT` - MySQL port (default: 3306)

## 📊 Monitoring

### Check Container Health

```bash
docker ps
docker exec ktl-booking supervisorctl status
```

### View Logs

```bash
# All logs
docker logs ktl-booking

# Nginx logs
docker exec ktl-booking tail -f /var/log/nginx/error.log

# PHP-FPM logs
docker exec ktl-booking tail -f /var/log/php-fpm.log

# Scheduler logs
docker exec ktl-booking tail -f /var/www/html/storage/logs/scheduler.log

# Laravel logs
docker exec ktl-booking tail -f /var/www/html/storage/logs/laravel.log
```

### Verify Services

```bash
# Check all services
docker exec ktl-booking supervisorctl status

# Should show:
# nginx        RUNNING
# php-fpm      RUNNING
# scheduler    RUNNING
```

## 🔄 Updates and Maintenance

### Update to Latest Version

```bash
# Pull latest code
git pull

# Rebuild image
docker build -f Dockerfile.production -t ktl-booking:latest .

# Stop old container
docker stop ktl-booking
docker rm ktl-booking

# Start new container
docker run -d --name ktl-booking ... ktl-booking:latest
```

### Run Migrations

```bash
docker exec ktl-booking php artisan migrate --force
```

### Clear Cache

```bash
docker exec ktl-booking php artisan optimize:clear
docker exec ktl-booking php artisan optimize
```

## 🐛 Troubleshooting

### Container Starts But App Not Working

Check logs:
```bash
docker logs ktl-booking --tail 100
```

Restart services:
```bash
docker exec ktl-booking supervisorctl restart all
```

### Scheduler Not Running Tasks

```bash
# Check scheduler status
docker exec ktl-booking supervisorctl status scheduler

# Check scheduler logs
docker exec ktl-booking tail -f /var/www/html/storage/logs/scheduler.log

# Restart scheduler
docker exec ktl-booking supervisorctl restart scheduler
```

### Database Connection Issues

```bash
# Test database connection
docker exec ktl-booking php artisan tinker --execute="DB::connection()->getPdo();"

# Check environment variables
docker exec ktl-booking env | grep DB_
```

## 🔒 Security Best Practices

1. **Never commit .env file** - Use platform environment variables
2. **Use strong APP_KEY** - Generate with `php artisan key:generate`
3. **Use managed database** - Don't run MySQL in same container for production
4. **Enable HTTPS** - Use platform SSL/TLS termination
5. **Set APP_DEBUG=false** - Never enable debug in production
6. **Regular updates** - Keep dependencies updated

## ✅ Production Checklist

Before going live:
- [ ] APP_KEY generated and set
- [ ] APP_ENV=production
- [ ] APP_DEBUG=false
- [ ] Database credentials secured
- [ ] HTTPS enabled
- [ ] Health checks passing
- [ ] Scheduler running
- [ ] Backups configured
- [ ] Monitoring setup
- [ ] Error tracking enabled (Sentry, etc.)

---

**Need help?** Check logs first, then review this guide. The single container makes deployment and debugging much simpler!
