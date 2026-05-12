#!/bin/bash
# DAZO Storage Manager (Atomic Restores & Rollbacks)
# Handles files_a and files_b with symlink switching

set -e

STORAGE_PATH="/var/www/html/storage/app"
SLOT_A="$STORAGE_PATH/files_a"
SLOT_B="$STORAGE_PATH/files_b"
PUBLIC_LINK="$STORAGE_PATH/public"

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

case "$1" in
    init)
        echo -e "${YELLOW}Initializing storage slots...${NC}"
        mkdir -p "$SLOT_A" "$SLOT_B"
        
        # If storage/app/public is a directory, move its content to slot_a and make it a link
        if [ ! -L "$PUBLIC_LINK" ] && [ -d "$PUBLIC_LINK" ]; then
            echo -e "${YELLOW}Converting storage/app/public to symlink...${NC}"
            cp -r "$PUBLIC_LINK/." "$SLOT_A/"
            rm -rf "$PUBLIC_LINK"
            ln -s "$SLOT_A" "$PUBLIC_LINK"
        elif [ ! -e "$PUBLIC_LINK" ]; then
            ln -s "$SLOT_A" "$PUBLIC_LINK"
        fi
        echo -e "${GREEN}Storage initialized.${NC}"
        ;;

    get-active)
        CURRENT=$(readlink -f "$PUBLIC_LINK")
        if [[ "$CURRENT" == *"/files_a" ]]; then
            echo "a"
        else
            echo "b"
        fi
        ;;

    restore)
        ZIP_FILE="$2"
        TARGET_SLOT="$3" # 'a' or 'b'
        
        if [ -z "$ZIP_FILE" ] || [ -z "$TARGET_SLOT" ]; then
            echo -e "${RED}Usage: $0 restore [zip_path] [a|b]${NC}"
            exit 1
        fi
        
        DEST_DIR=$([ "$TARGET_SLOT" == "a" ] && echo "$SLOT_A" || echo "$SLOT_B")
        
        echo -e "${YELLOW}Restoring files to slot $TARGET_SLOT ($DEST_DIR)...${NC}"
        # Clear target slot completely (including hidden files)
        rm -rf "$DEST_DIR"
        mkdir -p "$DEST_DIR"
        # Unzip
        unzip -q -o "$ZIP_FILE" -d "$DEST_DIR"
        echo -e "${GREEN}Restore complete in slot $TARGET_SLOT.${NC}"
        ;;

    switch)
        TARGET_SLOT="$2" # 'a' or 'b'
        DEST_DIR=$([ "$TARGET_SLOT" == "a" ] && echo "$SLOT_A" || echo "$SLOT_B")
        
        echo -e "${YELLOW}Switching active storage to slot $TARGET_SLOT...${NC}"
        ln -sf "$DEST_DIR" "$PUBLIC_LINK"
        echo -e "${GREEN}Active storage is now $TARGET_SLOT.${NC}"
        ;;

    *)
        echo "Usage: $0 {init|get-active|restore|switch}"
        exit 1
        ;;
esac
