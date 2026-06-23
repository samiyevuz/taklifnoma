#!/usr/bin/env bash
set -euo pipefail

APP="/var/www/taklifnoma/data/www/taklifnoma.net"
cd "$APP"

cp -a .env ".env.manual-backup-$(date +%Y%m%d-%H%M%S)"

mkdir -p "$HOME/.ssh"
chmod 700 "$HOME/.ssh"

if ! grep -q "Host github.com-taklifnoma" "$HOME/.ssh/config" 2>/dev/null; then
  cat >> "$HOME/.ssh/config" <<'EOF'

Host github.com-taklifnoma
  HostName github.com
  User git
  IdentityFile ~/.ssh/taklifnoma_deploy
  IdentitiesOnly yes
EOF
fi
chmod 600 "$HOME/.ssh/config"
ssh-keyscan -t ed25519 github.com >> "$HOME/.ssh/known_hosts" 2>/dev/null || true

git remote set-url origin git@github.com-taklifnoma:samiyevuz/taklifnoma.git
git fetch origin main
git reset --hard origin/main

if [[ ! -f .env ]]; then
  cp "$(ls -t .env.manual-backup-* | head -1)" .env
fi

chmod +x deploy/deploy.sh
bash deploy/deploy.sh

curl -sk -o /dev/null -w "UP:%{http_code} " https://taklifnoma.net/up
curl -sk -o /dev/null -w "HOME:%{http_code}\n" https://taklifnoma.net/
