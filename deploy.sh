#!/bin/bash

# Script de déploiement
# Usage: ./deploy.sh [--skip-maintenance]

set -e

REPO_URL="https://github.com/MichaMegretDeveloppementWeb/devigny-peinture.git"

SKIP_MAINTENANCE=false
if [ "$1" == "--skip-maintenance" ]; then
    SKIP_MAINTENANCE=true
fi

# Garantir la désactivation du mode maintenance même en cas d'erreur
cleanup() {
    if [ "$SKIP_MAINTENANCE" = false ]; then
        echo "→ Désactivation du mode maintenance..."
        php artisan up 2>/dev/null || true
    fi
}
trap cleanup EXIT

echo "============================================"
echo "  Déploiement Devigny Peinture"
echo "============================================"

# 1. Vérifier que le remote origin pointe vers le bon dépôt
CURRENT_REMOTE=$(git remote get-url origin 2>/dev/null || echo "")
if [ "$CURRENT_REMOTE" != "$REPO_URL" ]; then
    echo "→ Correction du remote origin..."
    echo "  Ancien : $CURRENT_REMOTE"
    echo "  Nouveau : $REPO_URL"
    git remote set-url origin "$REPO_URL" 2>/dev/null || git remote add origin "$REPO_URL"
fi

# 2. Activer le mode maintenance
if [ "$SKIP_MAINTENANCE" = false ]; then
    echo "→ Activation du mode maintenance..."
    php artisan down --retry=60 || true
fi

# 3. Récupérer les dernières modifications
echo "→ Mise à jour du dépôt Git..."
git fetch origin
git reset --hard origin/main

# 4. Installer les dépendances PHP
echo "→ Installation des dépendances PHP..."
php composer.phar install --no-dev --optimize-autoloader --no-interaction

# 5. Installer les dépendances Node et compiler les assets
echo "→ Compilation des assets (Vite)..."
npm install --production=false
npm run build

# 6. Exécuter les migrations
echo "→ Exécution des migrations..."
php artisan migrate --force

# 7. Vider tous les caches Laravel
echo "→ Nettoyage des caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan event:clear

# 8. Optimiser les caches pour la production
echo "→ Optimisation des caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 9. Vérifier et corriger les permissions
echo "→ Correction des permissions..."
chmod -R 755 storage bootstrap/cache
find storage -type d -exec chmod 755 {} \;
find storage -type f -exec chmod 644 {} \;
find bootstrap/cache -type d -exec chmod 755 {} \;
find bootstrap/cache -type f -exec chmod 644 {} \;

echo ""
echo "============================================"
echo "  Déploiement terminé avec succès !"
echo "============================================"
echo ""
echo "Version déployée : $(git rev-parse --short HEAD)"
echo "Branche : $(git branch --show-current)"
echo "Date : $(date '+%Y-%m-%d %H:%M:%S')"
