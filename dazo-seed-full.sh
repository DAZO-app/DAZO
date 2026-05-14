#!/bin/bash
# DAZO Full Simulation Seeder helper
set -e

GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
RED='\033[0;31m'
NC='\033[0m'

FRESH=false
FULL=false
SUMMARY=false
GENERATE_ATTACHMENTS=false
ATTACHMENT_COUNT=80
NO_CONFIRM=false
RETURN_MENU=true

APP_EXEC="docker compose exec app"

print_usage() {
    echo -e "${BLUE}DAZO Full Simulation Seeder${NC}"
    echo ""
    echo "Usage:"
    echo "  ./dazo-seed-full.sh --full"
    echo "  ./dazo-seed-full.sh --fresh --full --summary"
    echo "  ./dazo-seed-full.sh --attachments --fresh --full --summary"
    echo "  ./dazo-seed-full.sh --summary"
    echo ""
    echo "Options:"
    echo "  --fresh       Réinitialise la base avec php artisan migrate:fresh"
    echo "  --full        Lance php artisan db:seed --class=FullSimulationSeeder"
    echo "  --summary     Affiche les volumes principaux après exécution"
    echo "  --attachments Génère le pool de pièces jointes texte avant le seed"
    echo "  --attachment-count N  Nombre de fichiers du pool PJ (défaut: 80)"
    echo "  --no-confirm  Désactive les confirmations interactives"
    echo "  --no-menu     Ne revient pas à dazo-tool.sh en fin d'exécution"
    echo "  -h, --help    Affiche cette aide"
}

confirm_or_exit() {
    local message="$1"

    if [ "$NO_CONFIRM" = true ]; then
        return
    fi

    echo -en "${YELLOW}${message} [y/N] : ${NC}"
    read answer

    case "$answer" in
        y|Y|yes|YES|o|O|oui|OUI) ;;
        *)
            echo -e "${RED}Opération annulée.${NC}"
            exit 0
            ;;
    esac
}

ensure_containers() {
    if ! docker compose ps app 2>/dev/null | grep -q "Up"; then
        echo -e "${YELLOW}🐳 Le container app ne semble pas démarré. Démarrage des containers...${NC}"
        docker compose up -d
    fi
}

show_interactive_menu() {
    clear
    echo -e "${BLUE}======================================================${NC}"
    echo -e "${BLUE}              DAZO FULL SIMULATION SEEDER${NC}"
    echo -e "${BLUE}======================================================${NC}"
    echo ""
    echo -e " 1) Seed full simulation uniquement"
    echo -e " 2) Générer les pièces jointes texte"
    echo -e " 3) Générer PJ, reset DB, seed full simulation"
    echo -e " 4) Reset DB, seed full simulation, puis résumé"
    echo -e " 5) Résumé seulement"
    echo -e " q) Retour"
    echo ""
    echo -en "${GREEN}Action souhaitée [1-4 / q] (défaut: q) : ${NC}"
    read choice

    case "${choice:-q}" in
        1) FULL=true ;;
        2) GENERATE_ATTACHMENTS=true ;;
        3) GENERATE_ATTACHMENTS=true; FRESH=true; FULL=true ;;
        4) FRESH=true; FULL=true; SUMMARY=true ;;
        5) SUMMARY=true ;;
        q|Q) exit 0 ;;
        *)
            echo -e "${RED}Choix invalide.${NC}"
            sleep 1
            exec ./dazo-seed-full.sh
            ;;
    esac
}

run_summary() {
    echo -e "${YELLOW}📊 Résumé des données principales...${NC}"
    $APP_EXEC php artisan tinker --execute='
        echo "Users: " . App\Models\User::count() . PHP_EOL;
        echo "Circles: " . App\Models\Circle::count() . PHP_EOL;
        echo "Decisions: " . App\Models\Decision::count() . PHP_EOL;
        echo "Public decisions: " . App\Models\Decision::where("visibility", "public")->count() . PHP_EOL;
        echo "Private decisions: " . App\Models\Decision::where("visibility", "private")->count() . PHP_EOL;
        echo "Decision versions: " . App\Models\DecisionVersion::count() . PHP_EOL;
        echo "Participants: " . App\Models\DecisionParticipant::count() . PHP_EOL;
        echo "Feedbacks: " . App\Models\Feedback::count() . PHP_EOL;
        echo "Consents: " . App\Models\Consent::count() . PHP_EOL;
        echo "Relations: " . App\Models\DecisionRelation::count() . PHP_EOL;
        echo "User settings: " . App\Models\DecisionUserSetting::count() . PHP_EOL;
        echo "Attachments: " . App\Models\Attachment::count() . PHP_EOL;
    '
}

while [ $# -gt 0 ]; do
    case "$1" in
        --fresh) FRESH=true; shift ;;
        --full) FULL=true; shift ;;
        --summary) SUMMARY=true; shift ;;
        --attachments) GENERATE_ATTACHMENTS=true; shift ;;
        --attachment-count)
            ATTACHMENT_COUNT="$2"
            shift 2
            ;;
        --no-confirm) NO_CONFIRM=true; shift ;;
        --no-menu) RETURN_MENU=false; shift ;;
        -h|--help)
            print_usage
            exit 0
            ;;
        *)
            echo -e "${RED}Option inconnue : $1${NC}"
            print_usage
            exit 1
            ;;
    esac
done

if [ "$FRESH" = false ] && [ "$FULL" = false ] && [ "$SUMMARY" = false ]; then
    show_interactive_menu
fi

ensure_containers

if [ "$GENERATE_ATTACHMENTS" = true ]; then
    echo -e "${YELLOW}📎 Génération du pool de pièces jointes texte...${NC}"
    ./dazo-generate-fake-attachments.sh --count "$ATTACHMENT_COUNT" --clean --no-menu
fi

if [ "$FRESH" = true ]; then
    confirm_or_exit "Cette action va supprimer et recréer toutes les tables. Continuer ?"
    echo -e "${YELLOW}🗄️  Reset de la base...${NC}"
    $APP_EXEC php artisan migrate:fresh
fi

if [ "$FULL" = true ]; then
    echo -e "${YELLOW}🌱 Lancement du full simulation seeder...${NC}"
    $APP_EXEC php artisan db:seed --class=FullSimulationSeeder
fi

if [ "$SUMMARY" = true ]; then
    run_summary
fi

echo -e "${GREEN}✅ Opération seed terminée.${NC}"

if [ "$RETURN_MENU" = true ] && [ "$NO_CONFIRM" = false ]; then
    echo -e "\n${YELLOW}Appuyez sur Entrée pour revenir au menu...${NC}"
    read
    exec ./dazo-tool.sh
fi
