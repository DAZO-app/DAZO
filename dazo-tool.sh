#!/bin/bash
# DAZO Unified Management Tool
# This script provides a menu-driven interface for all DAZO management tasks.

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
RED='\033[0;31m'
NC='\033[0m'

clear
echo -e "${BLUE}======================================================${NC}"
echo -e "${BLUE}         🏗️  DAZO UNIFIED MANAGEMENT TOOL${NC}"
echo -e "${BLUE}======================================================${NC}"
echo -e "Choisissez une action à effectuer :"
echo ""

echo -e "${YELLOW}--- INSTALLATION & MISE À JOUR ---${NC}"
echo -e " 1)  [Installation] ./dazo-install.sh        - Installation complète initiale"
echo -e " 2)  [Mise à jour]  ./dazo-update.sh         - Déploiement (Git pull + Build + Migrations)"
echo -e " 3)  [Rollback]     ./dazo-rollback.sh       - Retour à la version précédente (Git)"

echo ""
echo -e "${YELLOW}--- DIAGNOSTIC & SANTÉ ---${NC}"
echo -e " 4)  [Diag Global]  ./dazo-check.sh          - État des containers, DB, et logs"
echo -e " 5)  [Diag Stock]   ./dazo-diag-storage.sh   - Problèmes de logos ou pièces jointes"

echo ""
echo -e "${YELLOW}--- MAINTENANCE & CACHE ---${NC}"
echo -e " 6)  [Refresh]      ./dazo-refresh.sh        - Nettoyer caches et corriger les permissions"
echo -e " 7)  [Full Reset]   ./dazo-refresh.sh --restart --build - Redémarrer et reconstruire les assets"
echo -e " 8)  [Clean Cache]  ./dazo-cleancache.sh     - Purge brutale de tous les caches Laravel"
echo -e " 9)  [Clean DB]     ./dazo-cleanDBbackup.sh  - Supprimer les anciens backups de base de données"

echo ""
echo -e "${YELLOW}--- DÉVELOPPEMENT ---${NC}"
echo -e " 10) [Dev Mode]     ./dazo-dev.sh            - Lancer l'environnement avec Vite HMR"
echo -e " 11) [Git Pull]     ./dazo-gitpull.sh        - Récupérer le code sans l'appliquer"

echo ""
echo -e " q)  Quitter"
echo ""
echo -en "${GREEN}Action souhaitée [1-11 / q] : ${NC}"
read choice

case $choice in
    1) ./dazo-install.sh ;;
    2) ./dazo-update.sh ;;
    3) ./dazo-rollback.sh ;;
    4) ./dazo-check.sh ;;
    5) ./dazo-diag-storage.sh ;;
    6) ./dazo-refresh.sh ;;
    7) ./dazo-refresh.sh --restart --build ;;
    8) ./dazo-cleancache.sh ;;
    9) ./dazo-cleanDBbackup.sh ;;
    10) ./dazo-dev.sh ;;
    11) ./dazo-gitpull.sh ;;
    q|Q) exit 0 ;;
    *) echo -e "${RED}Choix invalide.${NC}" ; sleep 1 ; ./dazo-tool.sh ;;
esac
