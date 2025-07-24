#!/bin/bash

# ==========================================================================
# IOTCNT Database Backup Script
# ==========================================================================

set -e

# Configuration
BACKUP_DIR="/backups"
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_FILE="iotcnt_backup_${DATE}.sql"
RETENTION_DAYS=7

# Database configuration from environment
DB_HOST=${MYSQL_HOST:-database}
DB_USER=${MYSQL_USER:-root}
DB_PASSWORD=${MYSQL_PASSWORD}
DB_NAME=${MYSQL_DATABASE:-iotcnt}

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${GREEN}[INFO]${NC} Starting IOTCNT database backup..."

# Check if backup directory exists
if [ ! -d "$BACKUP_DIR" ]; then
    echo -e "${YELLOW}[WARNING]${NC} Backup directory doesn't exist. Creating..."
    mkdir -p "$BACKUP_DIR"
fi

# Check database connection
echo -e "${GREEN}[INFO]${NC} Testing database connection..."
if ! mysqladmin ping -h"$DB_HOST" -u"$DB_USER" -p"$DB_PASSWORD" --silent; then
    echo -e "${RED}[ERROR]${NC} Cannot connect to database!"
    exit 1
fi

# Create backup
echo -e "${GREEN}[INFO]${NC} Creating backup: $BACKUP_FILE"
mysqldump -h"$DB_HOST" -u"$DB_USER" -p"$DB_PASSWORD" \
    --single-transaction \
    --routines \
    --triggers \
    --events \
    --add-drop-database \
    --databases "$DB_NAME" > "$BACKUP_DIR/$BACKUP_FILE"

# Check if backup was successful
if [ $? -eq 0 ]; then
    echo -e "${GREEN}[SUCCESS]${NC} Backup created successfully!"

    # Compress backup
    echo -e "${GREEN}[INFO]${NC} Compressing backup..."
    gzip "$BACKUP_DIR/$BACKUP_FILE"
    BACKUP_FILE="${BACKUP_FILE}.gz"

    # Show backup size
    BACKUP_SIZE=$(du -h "$BACKUP_DIR/$BACKUP_FILE" | cut -f1)
    echo -e "${GREEN}[INFO]${NC} Backup size: $BACKUP_SIZE"

else
    echo -e "${RED}[ERROR]${NC} Backup failed!"
    exit 1
fi

# Clean old backups
echo -e "${GREEN}[INFO]${NC} Cleaning old backups (older than $RETENTION_DAYS days)..."
find "$BACKUP_DIR" -name "iotcnt_backup_*.sql.gz" -mtime +$RETENTION_DAYS -delete

# List remaining backups
echo -e "${GREEN}[INFO]${NC} Current backups:"
ls -lh "$BACKUP_DIR"/iotcnt_backup_*.sql.gz 2>/dev/null || echo "No backups found"

echo -e "${GREEN}[SUCCESS]${NC} Backup process completed!"

# Optional: Send notification (uncomment if needed)
# curl -X POST "https://api.telegram.org/bot$TELEGRAM_BOT_TOKEN/sendMessage" \
#     -d "chat_id=$TELEGRAM_ADMIN_CHAT_ID" \
#     -d "text=✅ IOTCNT Database backup completed successfully! File: $BACKUP_FILE Size: $BACKUP_SIZE"
