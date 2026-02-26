# Railway Deployment Guide

This guide explains how to deploy KTL Booking System to Railway as a test environment.

---

## 🚂 What is Railway?

Railway is a modern cloud platform that:
- Automatically deploys from GitHub on every push
- Provides managed MySQL database
- Free tier available (limited resources)
- Good for testing/staging environments

**Use Railway for:** Testing, staging, demos
**Use Unraid for:** Production, full control

---

## 📋 Prerequisites

1. **Railway Account:** https://railway.app (sign up free)
2. **GitHub Repository:** Connected to Railway
3. **Environment Variables:** Set in Railway dashboard

---

## 🚀 Deployment Methods

### Method 1: Automatic GitHub Deployment (Recommended)

**This is already set up!** Railway automatically deploys when you push to `main` branch.

**How it works:**
1. You push code to GitHub: `git push`
2. Railway detects the push
3. Builds using `Dockerfile`
4. Deploys automatically

**To monitor:**
```bash
# Install Railway CLI
npm i -g @railway/cli

# Link to your project
railway link

# Watch logs
railway logs
```

---

### Method 2: Manual Deployment via Script

**Use the deployment script:**

```bash
chmod +x deploy-railway.sh
./deploy-railway.sh
```

This script:
- Checks Railway configuration
- Shows current project status
- Triggers manual deployment

---

## ⚙️ Railway Configuration

### 1. Environment Variables

**Required variables in Railway dashboard:**

```bash
# App
APP_NAME="KTL Booking System"
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:... # Generate with: php artisan key:generate --show
APP_URL=https://your-app.up.railway.app

# Database (Railway MySQL)
DB_CONNECTION=mysql
DB_HOST=${{ MYSQLHOST }}  # Railway variable
DB_PORT=${{ MYSQLPORT }}  # Railway variable
DB_DATABASE=${{ MYSQLDATABASE }}  # Railway variable
DB_USERNAME=${{ MYSQLUSER }}  # Railway variable
DB_PASSWORD=${{ MYSQLPASSWORD }}  # Railway variable

# Session & Cache
SESSION_DRIVER=database
CACHE_DRIVER=database
QUEUE_CONNECTION=database

# Timezone
APP_TIMEZONE="Europe/London"
```

**How to set:**
1. Go to Railway dashboard
2. Select your project
3. Click **Variables** tab
4. Add each variable

---

### 2. Add MySQL Database

**In Railway dashboard:**
1. Click **New** → **Database** → **Add MySQL**
2. Railway automatically sets `${{ MYSQL* }}` variables
3. No manual database configuration needed!

---

### 3. Files for Railway

**Already configured:**
- ✅ `Dockerfile` - Builds the container
- ✅ `railway.json` - Railway configuration
- ✅ `supervisord.conf` - Runs Laravel + Cron
- ✅ `.railwayignore` - Files to exclude

---

## 🐳 What the Dockerfile Does

The `Dockerfile` for Railway:

1. ✅ **Installs PHP 8.4** with all required extensions
2. ✅ **Installs cron** for scheduled tasks
3. ✅ **Installs supervisor** to manage processes
4. ✅ **Installs Node.js** for asset compilation
5. ✅ **Installs Composer** for PHP dependencies
6. ✅ **Runs migrations** on startup
7. ✅ **Starts cron daemon** (scheduled tasks work!)
8. ✅ **Starts Laravel server** on Railway's PORT

---

## 🔄 Cron Jobs on Railway

**Cron jobs work automatically!**

The Dockerfile sets up:
- Cron daemon running in background (via supervisor)
- Laravel scheduler running every minute
- Logs to `storage/logs/scheduler.log`

**Scheduled tasks that run:**
- `slots:generate` - Daily at 00:15
- `app:auto-release-slots` - Every 15 minutes
- `bays:sync-occupancy` - Every 30 minutes
- `bookings:cleanup-incomplete` - Every 15 minutes

**No manual cron setup needed!**

---

## 📊 Monitoring Deployment

### View Logs in Real-Time

```bash
# Install Railway CLI
npm i -g @railway/cli

# Link to your project (one time)
railway link

# Watch deployment logs
railway logs

# Watch Laravel logs
railway logs --filter laravel

# Watch cron logs
railway logs --filter cron
```

---

### Check Deployment Status

**Via CLI:**
```bash
railway status
```

**Via Dashboard:**
1. Go to https://railway.app
2. Select your project
3. Click **Deployments** tab
4. See build logs and status

---

## 🐛 Troubleshooting

### Deployment Fails

**Check build logs:**
```bash
railway logs
```

**Common issues:**
- Missing environment variables
- Database not connected
- Composer install fails (memory limit)

**Solution:**
1. Check all environment variables are set
2. Ensure MySQL database is added
3. Check Railway dashboard for error messages

---

### Cron Not Running

**Check if cron is running:**
```bash
railway run bash
# Inside container:
service cron status
crontab -l
```

**Check scheduler logs:**
```bash
railway logs --filter scheduler
```

---

### Database Connection Fails

**Verify database variables:**
```bash
railway variables
```

**Should show:**
- `MYSQLHOST`
- `MYSQLPORT`
- `MYSQLDATABASE`
- `MYSQLUSER`
- `MYSQLPASSWORD`

**Fix:**
1. Add MySQL database in Railway dashboard
2. Railway auto-sets these variables
3. Reference them in your environment variables (see above)

---

### Port Issues

**Railway dynamically assigns PORT.**

The Dockerfile uses: `--port=${PORT:-8080}`

This means:
- Railway sets `$PORT` variable (usually 8080 or random)
- Dockerfile uses it automatically
- No manual configuration needed

---

## 🔧 Manual Railway Commands

### Deploy Manually

```bash
railway up
```

### Run Migrations

```bash
railway run php artisan migrate --force
```

### Generate App Key

```bash
railway run php artisan key:generate --show
# Copy the output and add to Railway environment variables
```

### Open App in Browser

```bash
railway open
```

### SSH into Container

```bash
railway run bash
```

---

## 📝 Deployment Checklist

Before deploying to Railway:

- [ ] Railway account created
- [ ] GitHub repository connected
- [ ] MySQL database added in Railway
- [ ] Environment variables set (APP_KEY, DB_*, etc.)
- [ ] `Dockerfile` exists
- [ ] `railway.json` exists
- [ ] `supervisord.conf` exists

**Then push to GitHub:**

```bash
git push origin main
```

Railway will auto-deploy!

---

## 🆚 Railway vs Unraid

| Feature | Railway | Unraid |
|---------|---------|--------|
| **Purpose** | Test/Staging | Production |
| **Deployment** | Auto on git push | Manual script |
| **Database** | Managed MySQL | Self-hosted MySQL |
| **Cron** | Auto-configured | Needs persistence script |
| **Cost** | Free tier limited | Self-hosted (free) |
| **Control** | Limited | Full control |
| **Speed** | Fast deploy (~2 min) | Slower deploy (~5 min) |

**Recommendation:**
- Use **Railway** for quick testing and demos
- Use **Unraid** for production and main system

---

## 🔄 Workflow

**Typical development workflow:**

1. **Develop locally** (Herd, Valet, Docker)
2. **Commit and push** to GitHub
3. **Railway auto-deploys** (test environment)
4. **Test on Railway** (staging)
5. **Deploy to Unraid** when ready (production)

```bash
# Local development
git add .
git commit -m "Add new feature"
git push  # Railway auto-deploys

# Test on Railway
railway open

# Deploy to production (Unraid)
ssh root@unraid-ip
cd /mnt/user/appdata/ktl-booking
git pull
./ktl-deploy.sh
```

---

## 🎯 Quick Start

**Deploy to Railway right now:**

1. **Push to GitHub:**
   ```bash
   git push origin main
   ```

2. **Go to Railway dashboard:**
   https://railway.app

3. **Click your project → Deployments**

4. **Wait 2-3 minutes for build**

5. **Click "Open App" button**

Done! Railway is live.

---

## 📞 Support

**Railway Issues:**
- Docs: https://docs.railway.app
- Discord: https://discord.gg/railway
- GitHub: https://github.com/railwayapp/railway

**KTL Booking Issues:**
- Check logs: `railway logs`
- Check status: `railway status`
- Run commands: `railway run <command>`

---

## 🚨 Important Notes

**Railway Limitations (Free Tier):**
- 500 hours/month execution time
- 512MB RAM
- 1GB disk
- Sleeps after 30 minutes inactivity

**Not suitable for:**
- High-traffic production
- Large databases
- 24/7 uptime requirements

**Perfect for:**
- Testing new features
- Demos and presentations
- Staging environment
- Development testing

---

## ✅ Summary

**Deployment is automatic!**

1. ✅ Push to GitHub
2. ✅ Railway builds from Dockerfile
3. ✅ Cron automatically configured
4. ✅ Migrations run on startup
5. ✅ App available at Railway URL

**No manual steps needed after initial setup!**

Check deployment status:
```bash
railway status
railway logs
railway open
```

---

## 🔗 Useful Links

- Railway Dashboard: https://railway.app
- Your App URL: https://your-app.up.railway.app
- GitHub Repository: https://github.com/londo320/KTL_BookingSystem
- Railway Docs: https://docs.railway.app
