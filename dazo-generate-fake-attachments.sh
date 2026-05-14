#!/usr/bin/env bash
# DAZO fake text attachment pool generator
set -e

GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
RED='\033[0;31m'
NC='\033[0m'

COUNT=80
OUT_DIR="./storage/app/private/attachments/seed-pool"
CLEAN=false
RETURN_MENU=true

print_usage() {
    echo -e "${BLUE}DAZO Fake Attachments Generator${NC}"
    echo ""
    echo "Usage:"
    echo "  ./dazo-generate-fake-attachments.sh"
    echo "  ./dazo-generate-fake-attachments.sh --count 120 --clean"
    echo "  ./dazo-generate-fake-attachments.sh --out ./storage/app/private/attachments/seed-pool"
    echo ""
    echo "Options:"
    echo "  --count N     Nombre de fichiers texte à générer (défaut: 80)"
    echo "  --out DIR     Dossier de sortie (défaut: storage/app/private/attachments/seed-pool)"
    echo "  --clean       Vide le dossier de sortie avant génération"
    echo "  --no-menu     Ne revient pas à dazo-tool.sh en fin d'exécution"
    echo "  -h, --help    Affiche cette aide"
}

while [ $# -gt 0 ]; do
    case "$1" in
        --count)
            COUNT="$2"
            shift 2
            ;;
        --out)
            OUT_DIR="$2"
            shift 2
            ;;
        --clean)
            CLEAN=true
            shift
            ;;
        --no-menu)
            RETURN_MENU=false
            shift
            ;;
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

if ! [[ "$COUNT" =~ ^[0-9]+$ ]] || [ "$COUNT" -lt 1 ]; then
    echo -e "${RED}--count doit être un entier positif.${NC}"
    exit 1
fi

if [ "$CLEAN" = true ]; then
    echo -e "${YELLOW}🧹 Nettoyage du pool : $OUT_DIR${NC}"
    rm -rf "$OUT_DIR"
fi

echo -e "${YELLOW}📎 Génération de $COUNT pièces jointes texte dans : $OUT_DIR${NC}"

# On exécute la génération via Docker pour éviter les problèmes de permissions hôte/container
docker compose exec -T app python3 - "$COUNT" "$OUT_DIR" <<'PYEOF'
import csv
import json
import os
import sys
from datetime import datetime, timedelta

count = int(sys.argv[1])
out_dir = sys.argv[2]

# On s'assure que le dossier existe avec les bons droits (exécuté dans le container)
os.makedirs(out_dir, exist_ok=True)

subjects = [
    "analyse-impact",
    "synthese-objection",
    "budget-previsionnel",
    "compte-rendu",
    "note-risque",
    "plan-action",
    "benchmark",
    "decision-brief",
    "journal-atelier",
    "annexe-conformite",
]

def write(path, content):
    with open(path, "w", encoding="utf-8", newline="") as handle:
        handle.write(content)

for index in range(1, count + 1):
    subject = subjects[(index - 1) % len(subjects)]
    stamp = f"{index:03d}"
    ext_cycle = index % 4
    fake_date = (datetime.now() - timedelta(days=index % 90)).strftime("%Y-%m-%d")

    if ext_cycle == 0:
        filename = f"{subject}-{stamp}.json"
        content = json.dumps({
            "reference": f"DAZO-SEED-{stamp}",
            "type": subject,
            "date": fake_date,
            "summary": "Document de test généré pour les pièces jointes du seeder full simulation.",
            "risk_level": ["low", "medium", "high"][index % 3],
            "items": [
                {"label": "impact", "value": index % 5 + 1},
                {"label": "effort", "value": index % 8 + 1},
            ],
        }, ensure_ascii=False, indent=2)
        write(os.path.join(out_dir, filename), content + "\n")
    elif ext_cycle == 1:
        filename = f"{subject}-{stamp}.md"
        content = f"""# {subject.replace('-', ' ').title()} {stamp}

Date: {fake_date}

## Contexte

Ce fichier sert de pièce jointe de test pour le seeder full simulation DAZO.

## Points clés

- Hypothèse principale numéro {index}
- Risque à suivre avant adoption
- Action recommandée pour le cercle concerné

## Conclusion

Document généré automatiquement, sans donnée réelle.
"""
        write(os.path.join(out_dir, filename), content)
    elif ext_cycle == 2:
        filename = f"{subject}-{stamp}.csv"
        path = os.path.join(out_dir, filename)
        with open(path, "w", encoding="utf-8", newline="") as handle:
            writer = csv.writer(handle)
            writer.writerow(["reference", "date", "poste", "montant", "statut"])
            for row in range(1, 6):
                writer.writerow([
                    f"DAZO-{stamp}-{row}",
                    fake_date,
                    ["audit", "support", "formation", "outil", "atelier"][row - 1],
                    250 + (index * row) % 3000,
                    ["prévu", "validé", "à arbitrer"][row % 3],
                ])
    else:
        filename = f"{subject}-{stamp}.txt"
        content = f"""Référence: DAZO-SEED-{stamp}
Date: {fake_date}
Sujet: {subject}

Résumé:
Cette pièce jointe texte est générée pour tester l'affichage, le téléchargement,
les droits d'accès et la visibilité publique/privée des documents dans DAZO.

Notes:
- fichier sans donnée personnelle réelle
- compatible avec Storage::disk('local')
- destiné au seeder FullSimulationSeeder
"""
        write(os.path.join(out_dir, filename), content)

print(f"{count} fichier(s) généré(s) dans {out_dir}")
PYEOF

echo ""
echo -e "${GREEN}✅ Pool de pièces jointes généré.${NC}"
echo -e "${YELLOW}📁 Dossier : $OUT_DIR${NC}"
docker compose exec app ls "$OUT_DIR" | awk -F'.' '{print $NF}' | sort | uniq -c | awk '{printf "   %-6s : %s fichier(s)\n", $2, $1}'

if [ "$RETURN_MENU" = true ]; then
    echo -e "\n${YELLOW}Appuyez sur Entrée pour revenir au menu...${NC}"
    read
    exec ./dazo-tool.sh
fi
