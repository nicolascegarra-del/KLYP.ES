FROM nginx:alpine

# Puerto por defecto (sobreescribible desde Coolify)
ENV PORT=80

# Archivos estáticos
COPY index.html styles.css logo.png /usr/share/nginx/html/

# Plantilla de configuración de nginx
COPY nginx.conf /etc/nginx/conf.d/default.conf.template

# Script de arranque que aplica las variables de entorno
COPY docker-entrypoint.sh /docker-entrypoint.sh
RUN chmod +x /docker-entrypoint.sh

EXPOSE ${PORT}

ENTRYPOINT ["/docker-entrypoint.sh"]
