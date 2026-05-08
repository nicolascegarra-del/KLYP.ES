#!/bin/bash
set -e

# Generate db.php immediately so Apache can start without waiting for MySQL
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

# Import schema in background once MySQL user is ready
(
    TRIES=0
    until mysql --skip-ssl -h"${KLYP_DB_HOST}" -u"${KLYP_DB_USER}" -p"${KLYP_DB_PASS}" \
          -e "SELECT 1" "${KLYP_DB_NAME}" >/dev/null 2>&1; do
        TRIES=$((TRIES + 1))
        [ $TRIES -ge 200 ] && exit 0
        sleep 3
    done
    # Always apply DDL (CREATE TABLE IF NOT EXISTS) — safe to re-run on every deploy
    awk '/^-- Datos iniciales/{exit} {print}' /var/www/html/init.sql \
        | grep -v -iE "^(create database|use )" \
        | mysql --skip-ssl -h"${KLYP_DB_HOST}" -u"${KLYP_DB_USER}" -p"${KLYP_DB_PASS}" "${KLYP_DB_NAME}"

    # Only seed initial data when the database is empty (first deploy or after volume wipe).
    # Skipping this on existing installs preserves all admin panel configuration.
    CONFIG_EXISTS=$(mysql --skip-ssl -h"${KLYP_DB_HOST}" -u"${KLYP_DB_USER}" -p"${KLYP_DB_PASS}" \
        "${KLYP_DB_NAME}" -sNe "SELECT COUNT(*) FROM configuracion;" 2>/dev/null || echo "0")

    if [ "${CONFIG_EXISTS}" = "0" ]; then
        awk '/^-- Datos iniciales/{found=1} found{print}' /var/www/html/init.sql \
            | mysql --skip-ssl -h"${KLYP_DB_HOST}" -u"${KLYP_DB_USER}" -p"${KLYP_DB_PASS}" "${KLYP_DB_NAME}"
    fi
) &

exec "$@"
