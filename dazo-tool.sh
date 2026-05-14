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
echo -e " 12) [Update Dev]   ./dazo-updateDev.sh      - Build local + Cache (sans Git Pull)"

echo ""
echo -e "${YELLOW}--- DONNÉES DE TEST ---${NC}"
echo -e " 13) [Seed Full]    ./dazo-seed-full.sh --full              - Générer les données full simulation"
echo -e " 14) [Fresh+Seed]   ./dazo-seed-full.sh --fresh --full      - Reset DB puis données full simulation"
echo -e " 15) [Seed Summary] ./dazo-seed-full.sh --summary           - Afficher les volumes seedés"
echo -e " 16) [Seed Menu]    ./dazo-seed-full.sh                     - Menu avancé du seeder"
echo -e " 17) [Fake PJ]      ./dazo-generate-fake-attachments.sh     - Générer le pool de pièces jointes texte"
echo -e " 18) [PJ+FreshSeed] ./dazo-seed-full.sh --attachments --fresh --full - PJ + reset + seed"

echo ""
echo -en "${GREEN}Action souhaitée [1-18 / q] (défaut: q) : ${NC}"
read choice

# Default to 'q' if empty
if [ -z "$choice" ]; then
    choice="q"
fi

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
    12) ./dazo-updateDev.sh ;;
    13) ./dazo-seed-full.sh --full ;;
    14) ./dazo-seed-full.sh --fresh --full ;;
    15) ./dazo-seed-full.sh --summary ;;
    16) ./dazo-seed-full.sh ;;
    17) ./dazo-generate-fake-attachments.sh ;;
    18) ./dazo-seed-full.sh --attachments --fresh --full ;;
    q|Q) exit 0 ;;
    *) echo -e "${RED}Choix invalide.${NC}" ; sleep 1 ; exec ./dazo-tool.sh ;;
esac
