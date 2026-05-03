#!/bin/bash
# DAZO Database Backup Cleanup Script
# This script manages the deletion of old database backups.

BACKUP_DIR="./backups"
PATTERN="db_backup_*.sql"

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m'

# Ensure backup directory exists
if [ ! -d "$BACKUP_DIR" ]; then
    echo -e "${RED}❌ Le répertoire de backup $BACKUP_DIR n'existe pas.${NC}"
    exit 1
fi

clear
echo -e "${BLUE}======================================================${NC}"
echo -e "${BLUE}         🧹 DAZO DATABASE BACKUP CLEANUP${NC}"
echo -e "${BLUE}======================================================${NC}"
echo -e "Répertoire : $BACKUP_DIR"
echo -e "Nombre actuel de backups : $(ls -1 $BACKUP_DIR/$PATTERN 2>/dev/null | wc -l)"
echo ""
echo -e "Choisissez une option de nettoyage :"
echo ""
echo -e " 1) Supprimer TOUS les backups"
echo -e " 2) Conserver uniquement les 5 derniers backups"
echo -e " 3) Conserver uniquement les backups de la dernière journée (24h)"
echo -e " 4) Conserver uniquement les backups de la dernière semaine (7j)"
echo -e " 5) Conserver uniquement les backups du dernier mois (30j)"
echo -e " q) Annuler et retour"
echo ""
echo -en "${GREEN}Votre choix [1-5 / q] : ${NC}"
read choice

case $choice in
    1)
        echo -e "${RED}⚠️  Suppression de tous les backups en cours...${NC}"
        rm -f $BACKUP_DIR/$PATTERN
        echo -e "${GREEN}✅ Tous les backups ont été supprimés.${NC}"
        ;;
    2)
        echo -e "${YELLOW}🧹 Nettoyage pour ne garder que les 5 derniers...${NC}"
        # count files to avoid errors if less than 5
        FILES_COUNT=$(ls -1 $BACKUP_DIR/$PATTERN 2>/dev/null | wc -l)
        if [ "$FILES_COUNT" -gt 5 ]; then
            # Sort by time (newest first), skip top 5, delete rest
            ls -t $BACKUP_DIR/$PATTERN | tail -n +6 | xargs rm -f
            echo -e "${GREEN}✅ Terminé. Seuls les 5 plus récents ont été conservés.${NC}"
        else
            echo -e "${BLUE}ℹ️  Il y a déjà moins de 5 backups. Rien à faire.${NC}"
        fi
        ;;
    3)
        echo -e "${YELLOW}🧹 Nettoyage : suppression des backups de plus de 24h...${NC}"
        find $BACKUP_DIR -name "$PATTERN" -type f -mmin +1440 -delete -print
        echo -e "${GREEN}✅ Terminé.${NC}"
        ;;
    4)
        echo -e "${YELLOW}🧹 Nettoyage : suppression des backups de plus de 7 jours...${NC}"
        find $BACKUP_DIR -name "$PATTERN" -type f -mtime +6 -delete -print
        echo -e "${GREEN}✅ Terminé.${NC}"
        ;;
    5)
        echo -e "${YELLOW}🧹 Nettoyage : suppression des backups de plus de 30 jours...${NC}"
        find $BACKUP_DIR -name "$PATTERN" -type f -mtime +29 -delete -print
        echo -e "${GREEN}✅ Terminé.${NC}"
        ;;
    q|Q)
        exec ./dazo-tool.sh
        ;;
    *)
        echo -e "${RED}Choix invalide.${NC}"
        sleep 1
        exec $0 # Restart script
        ;;
esac

echo -e "\n${YELLOW}Appuyez sur Entrée pour revenir au menu...${NC}"
read
exec ./dazo-tool.sh
