#!/bin/sh
set -e

# Overwrite .env from environment variables so the app's custom loader picks them up
env > /var/www/html/.env

# Decompress SQLite databases on first boot
if [ -n "$(ls /var/www/html/databases/*.sqlite.gz 2>/dev/null)" ]; then
  for f in /var/www/html/databases/*.sqlite.gz; do
    code=$(basename "$f" .sqlite.gz)
    target="/var/www/html/storage/cache/sqlite/${code}.sqlite"
    if [ ! -f "$target" ]; then
      php -d memory_limit=512M -r "file_put_contents('$target', gzdecode(file_get_contents('$f')));"
    fi
  done
fi

exec apache2-foreground
