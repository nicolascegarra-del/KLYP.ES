#!/bin/sh
# Sustituye solo las variables de entorno definidas en nginx.conf (no las variables internas de nginx)
envsubst '${PORT}' < /etc/nginx/conf.d/default.conf.template > /etc/nginx/conf.d/default.conf
exec nginx -g "daemon off;"
