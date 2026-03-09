#!/bin/bash

# Script de déploiement — Agence TIC
# Usage: ./deploy.sh

set -e

REPO_URL="https://github.com/MichaMegretDeveloppementWeb/agence-tic.git"

# Garantir la désactivation du mode maintenance même en cas d'erreur
cleanup() {
    echo "→ Désactivation du mode maintenance..."
    php artisan up 2>/dev/null || true
}
trap cleanup EXIT

echo "============================================"
echo "  Déploiement Agence TIC"
echo "============================================"

# 1. Vérifier le remote origin
CURRENT_REMOTE=$(git remote get-url origin 2>/dev/null || echo "")
if [ "$CURRENT_REMOTE" != "$REPO_URL" ]; then
    echo "→ Correction du remote origin..."
    git remote set-url origin "$REPO_URL" 2>/dev/null || git remote add origin "$REPO_URL"
fi

# 2. Activer le mode maintenance
echo "→ Activation du mode maintenance..."
php artisan down --retry=60 || true

# 3. Récupérer les dernières modifications
echo "→ Mise à jour du dépôt Git..."
git fetch origin
git reset --hard origin/main

# 4. Installer les dépendances PHP
echo "→ Installation des dépendances PHP..."
php composer.phar install --no-dev --optimize-autoloader --no-interaction

# 5. Exécuter les migrations
echo "→ Exécution des migrations..."
php artisan migrate --force

# 6. Lien symbolique storage
echo "→ Vérification du lien storage..."
php artisan storage:link 2>/dev/null || true

# 7. Vider tous les caches
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

# 9. Permissions
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
