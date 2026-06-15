#!/bin/sh
set -e

# Overwrite .env from environment variables so the app's custom loader picks them up
env > /var/www/html/.env

# Decompress SQLite databases at runtime (avoids OOM during Docker build on HF)
for f in /var/www/html/databases/*.sqlite.gz; do
  code=$(basename "$f" .sqlite.gz)
  target="/var/www/html/storage/cache/sqlite/${code}.sqlite"
  if [ ! -f "$target" ]; then
    php -d memory_limit=512M -r "file_put_contents('$target', gzdecode(file_get_contents('$f')));"
  fi
done

exec apache2-foreground
