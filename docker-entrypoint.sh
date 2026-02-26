#!/bin/bash
set -e

# Start cron service
echo "Starting cron service..."
service cron start

# Start Apache in foreground (keeps container running)
echo "Starting Apache..."
apache2-foreground
