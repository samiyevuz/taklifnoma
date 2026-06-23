#!/usr/bin/env bash
set -euo pipefail

# One-time server setup for taklifnoma.net
# Run as the site user (taklifnoma):
#   bash deploy/server-init.sh

DEPLOY_PATH="${DEPLOY_PATH:-/var/www/taklifnoma/data/www/taklifnoma.net}"
REPO_URL="${REPO_URL:-git@github.com-taklifnoma:samiyevuz/taklifnoma.git}"
DEPLOY_KEY_PATH="${DEPLOY_KEY_PATH:-$HOME/.ssh/taklifnoma_deploy}"

echo "==> Initializing deployment at: $DEPLOY_PATH"

mkdir -p "$HOME/.ssh"
chmod 700 "$HOME/.ssh"

if [[ ! -f "$DEPLOY_KEY_PATH" ]]; then
    echo "==> Generating deploy key: $DEPLOY_KEY_PATH"
    ssh-keygen -t ed25519 -C "taklifnoma-deploy" -f "$DEPLOY_KEY_PATH" -N ""
    echo
    echo "==> Add this deploy key to GitHub (repo Settings -> Deploy keys -> Read access):"
    echo "----------------------------------------------------------------"
    cat "${DEPLOY_KEY_PATH}.pub"
    echo "----------------------------------------------------------------"
fi

if ! grep -q "Host github.com-taklifnoma" "$HOME/.ssh/config" 2>/dev/null; then
    cat >> "$HOME/.ssh/config" <<EOF

Host github.com-taklifnoma
    HostName github.com
    User git
    IdentityFile $DEPLOY_KEY_PATH
    IdentitiesOnly yes
EOF
    chmod 600 "$HOME/.ssh/config"
fi

ssh-keyscan -t ed25519 github.com >> "$HOME/.ssh/known_hosts" 2>/dev/null || true

mkdir -p "$(dirname "$DEPLOY_PATH")"

if [[ -d "$DEPLOY_PATH/.git" ]]; then
    echo "==> Repository already exists"
    cd "$DEPLOY_PATH"
    git remote set-url origin "$REPO_URL"
    git fetch origin main
    git reset --hard origin/main
else
    echo "==> Cloning repository..."
    git clone --branch main "$REPO_URL" "$DEPLOY_PATH"
    cd "$DEPLOY_PATH"
fi

if [[ ! -f .env ]]; then
  cp .env.example .env
  echo "==> Created .env from .env.example — configure before going live."
fi

chmod +x deploy/deploy.sh 2>/dev/null || true

echo "==> Server init complete."
echo "==> Next: ensure .env is configured (APP_URL=https://taklifnoma.net, DB_*, etc.)"
echo "==> Ensure the web server document root points to: $DEPLOY_PATH/public"
