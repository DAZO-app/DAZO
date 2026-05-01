#!/bin/bash
# DAZO Simple Git Pull
set -e

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo -e "${YELLOW}📥 Fetching latest changes...${NC}"
git fetch origin
echo -e "${YELLOW}👀 New commits found:${NC}"
git log HEAD..origin/main --oneline

echo -e "${YELLOW}🚀 Pulling main branch...${NC}"
git pull origin main
echo -e "${GREEN}✅ Pull complete${NC}"
