#!/bin/sh
set -e
mkdir -p /var/www/project/public/uploads/contacts
chown -R www-data:www-data /var/www/project/public/uploads || true
chmod -R 0775 /var/www/project/public/uploads || true
exec "$@"
