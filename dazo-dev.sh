#!/bin/bash
# DAZO Local Development Script
set -e

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo -e "${YELLOW}🚀 Starting DAZO Development Environment...${NC}"

# 1. Ensure containers are running
docker compose up -d

# 2. Start Vite Development Server in the background
echo -e "${YELLOW}📦 Starting Vite Development Server (HMR)...${NC}"
echo -e "${YELLOW}💡 Note: Leave this terminal open. Press Ctrl+C to stop.${NC}"

# Run npm run dev inside the app container
docker compose exec app npm run dev
