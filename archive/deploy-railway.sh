#!/bin/bash
set -e

echo "===== KTL Booking System - Railway Deployment ====="
echo "This script prepares the repository for Railway deployment"
echo ""

# Check if railway CLI is installed
if ! command -v railway &> /dev/null; then
    echo "⚠️  Railway CLI not found. Install it with:"
    echo "   npm i -g @railway/cli"
    echo ""
    echo "Or deploy via Railway dashboard (recommended):"
    echo "   1. Go to https://railway.app"
    echo "   2. Connect your GitHub repository"
    echo "   3. Railway will auto-deploy on push to main branch"
    exit 1
fi

echo "📋 Checking Railway configuration..."

# Check if railway.json exists
if [ ! -f "railway.json" ]; then
    echo "❌ railway.json not found!"
    exit 1
fi

# Check if Dockerfile exists
if [ ! -f "Dockerfile" ]; then
    echo "❌ Dockerfile not found!"
    exit 1
fi

echo "✅ Railway configuration files found"
echo ""

# Show current Railway project
echo "🚂 Railway Project Info:"
railway status || echo "⚠️  Not linked to Railway project. Run: railway link"
echo ""

# Ask for confirmation
read -p "Deploy to Railway now? (y/n) " -n 1 -r
echo
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    echo "Deployment cancelled."
    exit 0
fi

echo ""
echo "🚀 Deploying to Railway..."
echo ""

# Deploy using Railway CLI
railway up

echo ""
echo "✅ Deployment triggered!"
echo ""
echo "📊 Monitor deployment:"
echo "   railway logs"
echo ""
echo "🌐 Open dashboard:"
echo "   railway open"
echo ""
echo "⚙️ Environment variables needed in Railway:"
echo "   - APP_KEY (run: php artisan key:generate --show)"
echo "   - DB_CONNECTION=mysql"
echo "   - DB_HOST=<Railway MySQL host>"
echo "   - DB_PORT=3306"
echo "   - DB_DATABASE=railway"
echo "   - DB_USERNAME=<Railway MySQL user>"
echo "   - DB_PASSWORD=<Railway MySQL password>"
echo ""
