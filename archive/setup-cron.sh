#!/bin/bash
set -e

echo "╔════════════════════════════════════════════════════════╗"
echo "║   Laravel Scheduler (Cron) Setup Script               ║"
echo "╚════════════════════════════════════════════════════════╝"
echo ""

# Detect environment
if command -v docker &> /dev/null && [ -n "$(docker ps -q -f name=ktl-booking-app)" ]; then
    echo "🐳 Docker environment detected"
    CONTAINER_NAME="ktl-booking-app"
    DOCKER_MODE=true
else
    echo "💻 Local/Server environment detected"
    DOCKER_MODE=false
fi

# Function to add cron job
setup_cron() {
    if [ "$DOCKER_MODE" = true ]; then
        echo "⏰ Setting up cron inside Docker container..."

        # Install cron if not present
        docker exec "$CONTAINER_NAME" bash -c 'apt-get update -qq && apt-get install -y -qq cron' 2>&1 | grep -v "debconf: unable to initialize" || true

        # Start cron service
        docker exec "$CONTAINER_NAME" bash -c 'service cron start' || true

        # Add Laravel scheduler to crontab
        docker exec "$CONTAINER_NAME" bash -c 'echo "* * * * * cd /var/www/html && php artisan schedule:run >> /var/www/html/storage/logs/scheduler.log 2>&1" | crontab -'

        # Verify cron setup
        echo ""
        echo "✅ Cron job added:"
        docker exec "$CONTAINER_NAME" bash -c 'crontab -l'

        # Ensure cron runs on container restart
        docker exec "$CONTAINER_NAME" bash -c 'echo "#!/bin/bash\nservice cron start\napache2-foreground" > /start.sh && chmod +x /start.sh' || true

    else
        echo "⏰ Setting up cron for current user..."

        # Get the project directory
        PROJECT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
        PHP_PATH=$(which php)

        # Create cron job
        CRON_JOB="* * * * * cd $PROJECT_DIR && $PHP_PATH artisan schedule:run >> $PROJECT_DIR/storage/logs/scheduler.log 2>&1"

        # Check if cron job already exists
        if crontab -l 2>/dev/null | grep -F "artisan schedule:run" > /dev/null; then
            echo "⚠️  Laravel scheduler cron job already exists"
            echo "Current crontab:"
            crontab -l | grep "artisan schedule:run"
        else
            # Add to crontab
            (crontab -l 2>/dev/null; echo "$CRON_JOB") | crontab -
            echo "✅ Cron job added:"
            echo "$CRON_JOB"
        fi
    fi
}

# Show scheduled tasks
show_schedule() {
    echo ""
    echo "📋 Scheduled Tasks:"
    if [ "$DOCKER_MODE" = true ]; then
        docker exec -w /var/www/html "$CONTAINER_NAME" php artisan schedule:list
    else
        php artisan schedule:list
    fi
}

# Test scheduler
test_scheduler() {
    echo ""
    echo "🧪 Testing scheduler (running once)..."
    if [ "$DOCKER_MODE" = true ]; then
        docker exec -w /var/www/html "$CONTAINER_NAME" php artisan schedule:run
    else
        php artisan schedule:run
    fi
    echo "✅ Scheduler test completed"
}

# Main execution
setup_cron
show_schedule
test_scheduler

echo ""
echo "╔════════════════════════════════════════════════════════╗"
echo "║              CRON SETUP COMPLETE!                      ║"
echo "╚════════════════════════════════════════════════════════╝"
echo ""
echo "📊 Scheduled Jobs:"
echo "   • slots:generate        - Daily at 00:15 (12:15 AM)"
echo "   • app:auto-release-slots - Every 15 minutes"
echo "   • bays:sync-occupancy   - Every 30 minutes"
echo "   • bookings:cleanup-incomplete - Every 15 minutes"
echo ""
echo "📝 Logs: storage/logs/scheduler.log"
echo ""
echo "✅ Cron jobs will now run automatically!"
