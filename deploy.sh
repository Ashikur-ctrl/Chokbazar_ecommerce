#!/bin/bash
set -euo pipefail

# ── Deploy to chokbazar.com via rsync over SSH ──────────────────────────────
# Usage:
#   ./deploy.sh                          # dry run (shows what would change)
#   ./deploy.sh --run                    # actual sync to production
#   ./deploy.sh --run --migrate          # sync + run database migrations
#   ./deploy.sh --run --clear-cache      # sync + clear Laravel cache
#   ./deploy.sh --run --all              # sync + migrate + clear cache
#
# Requirements:
#   - SSH key-based auth set up for the server
#   - rsync installed locally and on the server
#
# Layout notes:
#   On cPanel, the document root (public_html) is typically at the same
#   level as the Laravel project root, or symlinked into public/. If your
#   server has the project at a different path, edit REMOTE_PATH below.

SERVER="chokbazar.com"
USER="chokbazar_admin"
REMOTE_PATH="/home/chokbazar_admin"

EXCLUDE=(
  --exclude=.env
  --exclude=.env.*
  --exclude=node_modules
  --exclude=vendor
  --exclude=.git
  --exclude=public_html
  --exclude=chokbazar_sync
  --exclude=public/storage
  --exclude=storage/logs/*.log
  --exclude=storage/framework/cache/data/*
  --exclude=storage/framework/sessions/*
  --exclude=storage/framework/views/*
  --exclude=deploy.sh
)

DO_MIGRATE=false
DO_CACHE=false

# Parse flags
for arg in "$@"; do
  case "$arg" in
    --run)     ;;
    --migrate) DO_MIGRATE=true  ;;
    --clear-cache) DO_CACHE=true ;;
    --all)     DO_MIGRATE=true; DO_CACHE=true ;;
  esac
done

if [[ "${1:-}" != "--run" ]]; then
  echo "════════════════════════════════════════════════"
  echo " DRY RUN — add --run to actually deploy"
  echo "════════════════════════════════════════════════"
  rsync -avz --delete "${EXCLUDE[@]}" --dry-run ./ "$USER@$SERVER:$REMOTE_PATH"
  echo ""
  echo "Ready. Run:  ./deploy.sh --run"
  echo "   or:       ./deploy.sh --run --all"
  exit 0
fi

echo "════════════════════════════════════════════════"
echo " Deploying to $USER@$SERVER:$REMOTE_PATH ..."
echo "════════════════════════════════════════════════"

rsync -avz --delete "${EXCLUDE[@]}" ./ "$USER@$SERVER:$REMOTE_PATH"

echo "✔ Files synced."

if $DO_CACHE; then
  echo "→ Clearing Laravel cache..."
  ssh "$USER@$SERVER" "cd $REMOTE_PATH && php artisan optimize:clear"
  echo "✔ Cache cleared."
fi

if $DO_MIGRATE; then
  echo "→ Running migrations..."
  ssh "$USER@$SERVER" "cd $REMOTE_PATH && php artisan migrate --force"
  echo "✔ Migrations complete."
fi

echo "════════════════════════════════════════════════"
echo " Deploy complete."
echo "════════════════════════════════════════════════"
