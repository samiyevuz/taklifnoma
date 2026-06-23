#!/usr/bin/env bash
set -euo pipefail

export NVM_DIR="${NVM_DIR:-$HOME/.nvm}"
# shellcheck disable=SC1091
[ -s "$NVM_DIR/nvm.sh" ] && . "$NVM_DIR/nvm.sh"

APP_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$APP_DIR"

echo "==> Deploy started at $(date -Is)"
echo "==> App directory: $APP_DIR"

if ! command -v php >/dev/null 2>&1; then
    echo "ERROR: php is not installed or not in PATH" >&2
    exit 1
fi

if ! command -v composer >/dev/null 2>&1; then
    echo "ERROR: composer is not installed or not in PATH" >&2
    exit 1
fi

if ! command -v npm >/dev/null 2>&1; then
    echo "ERROR: npm is not installed or not in PATH" >&2
    exit 1
fi

if [[ ! -f .env ]]; then
    echo "ERROR: .env file not found. Restore from backup or copy .env.example." >&2
    exit 1
fi

cp -a .env ".env.deploy-backup-$(date +%Y%m%d-%H%M%S)"

php artisan down --retry=60 || true

git fetch origin main
git reset --hard origin/main

if [[ ! -f .env ]]; then
    latest_backup="$(ls -t .env.deploy-backup-* 2>/dev/null | head -1 || true)"
    if [[ -n "$latest_backup" ]]; then
        cp -a "$latest_backup" .env
        echo "==> Restored .env from $latest_backup"
    else
        echo "ERROR: .env missing after git reset" >&2
        exit 1
    fi
fi

composer install \
    --no-interaction \
    --prefer-dist \
    --no-dev \
    --optimize-autoloader \
    --no-progress

npm install --no-audit --no-fund
npm run build

php artisan migrate --force
php artisan storage:link --force 2>/dev/null || true

php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache 2>/dev/null || true

php artisan queue:restart 2>/dev/null || true

chmod -R ug+rwx storage bootstrap/cache 2>/dev/null || true

php artisan up

echo "==> Deploy finished at $(date -Is)"
