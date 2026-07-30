#!/bin/sh
set -e

# Merge environment variables into .env without destroying existing config
# System env vars (Hugging Face secrets) take precedence over .env defaults
# Preserve original .env values (API URLs, etc.) not set as system env vars
if [ -f /var/www/html/.env ]; then
    cp /var/www/html/.env /var/www/html/.env.orig
fi

env > /var/www/html/.env

# Append original .env entries that are NOT already set as system env vars
if [ -f /var/www/html/.env.orig ]; then
    while IFS='=' read -r line || [ -n "$line" ]; do
        line=$(echo "$line" | tr -d '\r')
        case "$line" in
            ''|'#'*) continue;;
        esac
        key="${line%%=*}"
        if ! grep -q "^${key}=" /var/www/html/.env 2>/dev/null; then
            echo "$line" >> /var/www/html/.env
        fi
    done < /var/www/html/.env.orig
    rm -f /var/www/html/.env.orig
fi

# Decompress SQLite databases at runtime (avoids OOM during Docker build on HF)
for f in /var/www/html/databases/*.sqlite.gz; do
  code=$(basename "$f" .sqlite.gz)
  target="/var/www/html/storage/cache/sqlite/${code}.sqlite"
  if [ ! -f "$target" ]; then
    php -d memory_limit=512M -r "file_put_contents('$target', gzdecode(file_get_contents('$f')));"
  fi
done

exec apache2-foreground
