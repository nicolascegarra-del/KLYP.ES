#!/bin/bash
set -e

echo "Waiting for MySQL at ${KLYP_DB_HOST}..."
TRIES=0
until mysqladmin ping -h"${KLYP_DB_HOST}" -u"${KLYP_DB_USER}" -p"${KLYP_DB_PASS}" --silent 2>/dev/null; do
    TRIES=$((TRIES + 1))
    if [ $TRIES -ge 40 ]; then
        echo "MySQL not available after 2 minutes, aborting."
        exit 1
    fi
    sleep 3
done
echo "MySQL is ready."

echo "Importing schema..."
mysql -h"${KLYP_DB_HOST}" -u"${KLYP_DB_USER}" -p"${KLYP_DB_PASS}" "${KLYP_DB_NAME}" < /var/www/html/init.sql
echo "Schema ready."

# Generate db.php from environment variables
cat > /var/www/html/db.php <<PHP
<?php
try {
    \$pdo = new PDO(
        'mysql:host=${KLYP_DB_HOST};dbname=${KLYP_DB_NAME};charset=utf8mb4',
        '${KLYP_DB_USER}',
        '${KLYP_DB_PASS}',
        [
            PDO::ATTR_ERRMODE                  => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE       => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES         => false,
            PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
        ]
    );
} catch (PDOException \$e) {
    http_response_code(500);
    die('Error de conexión a la base de datos.');
}
PHP

exec "$@"
