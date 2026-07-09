#!/bin/bash
# Exit immediately if a command exits with a non-zero status
set -e

# Load global environment paths so non-interactive SSH can find docker, git, etc.
export PATH=$PATH:/usr/local/bin:/usr/bin:/bin:/usr/sbin:/sbin

echo "🚀 Starting Auto-Deployment Process for SANGGAR-WEB..."

# 1. Pull the latest code from Git
echo "📥 1. Fetching and resetting to latest code from GitHub..."
git fetch --all
git reset --hard origin/miomidev

# 2. Rebuild Docker Image & Recreate Container
echo "🐳 2. Rebuilding Docker Image and Restarting Container..."

# Safely stop and remove any old container to avoid name conflicts
docker stop sanggar-web || true
docker rm -f sanggar-web || true

# Check if modern 'docker compose' is supported, otherwise fallback to legacy
if docker compose version >/dev/null 2>&1; then
    echo "Detected: docker compose"
    docker compose up -d --build
else
    echo "Detected: docker-compose"
    docker-compose up -d --build
fi

# 3. Wait for the container to initialize
echo "⏳ 3. Waiting for container to stabilize..."
sleep 5

# 4. Fix storage permissions (chmod only — avoid chown to prevent git permission issues)
echo "🔐 4. Fixing storage permissions..."
docker exec -t sanggar-web chmod -R 777 /var/www/html/storage
docker exec -t sanggar-web chmod -R 777 /var/www/html/bootstrap/cache

# 5. Run database migrations
echo "🗄️ 5. Running database migrations inside the container..."
docker exec -t sanggar-web php artisan migrate --force

# 6. Clear and optimize Laravel caches
echo "🧹 6. Optimizing and clearing Laravel caches inside the container..."
docker exec -t sanggar-web php artisan optimize:clear
docker exec -t sanggar-web php artisan config:cache
docker exec -t sanggar-web php artisan route:cache
docker exec -t sanggar-web php artisan view:cache

echo "🎉 Auto-Deployment completed successfully for SANGGAR-WEB!"
