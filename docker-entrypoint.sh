#!/bin/bash
set -e

echo "Waiting for MySQL at ${KLYP_DB_HOST}..."
until mysqladmin ping -h"${KLYP_DB_HOST}" -u"${KLYP_DB_USER}" -p"${KLYP_DB_PASS}" --silent 2>/dev/null; do
    echo "MySQL not ready yet, retrying in 3s..."
    sleep 3
done
echo "MySQL is ready."

# Generate db.php from environment variables set in Coolify
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
