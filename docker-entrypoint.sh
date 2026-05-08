#!/bin/bash
set -e

echo "Waiting for MySQL..."
TRIES=0
until mysql -h"${KLYP_DB_HOST}" -u"${KLYP_DB_USER}" -p"${KLYP_DB_PASS}" -e "SELECT 1" "${KLYP_DB_NAME}" >/dev/null 2>&1; do
    TRIES=$((TRIES + 1))
    if [ $TRIES -ge 40 ]; then
        echo "MySQL not available after 2 minutes, aborting."
        exit 1
    fi
    sleep 3
done
echo "MySQL is ready."

echo "Importing schema..."
grep -v -iE "^create database|^use " /var/www/html/init.sql | \
    mysql -h"${KLYP_DB_HOST}" -u"${KLYP_DB_USER}" -p"${KLYP_DB_PASS}" "${KLYP_DB_NAME}"
echo "Schema ready."

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
