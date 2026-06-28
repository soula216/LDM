#!/usr/bin/env bash
set -euo pipefail

echo "==> Build assets Vite"
npm ci
npm run build

echo "==> Vérification build"
test -f public/build/manifest.json
ls -la public/build/assets/

echo "==> Cache Laravel"
php artisan view:clear
php artisan config:clear
php artisan cache:clear
php artisan config:cache

echo "==> OK — manifest:"
cat public/build/manifest.json
