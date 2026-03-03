# Usamos una imagen ligera de Nginx basada en Alpine Linux
FROM nginx:alpine

# Copiamos los archivos estáticos al directorio de Nginx
COPY . /usr/share/nginx/html

# Exponemos el puerto 80
EXPOSE 80

# Comando para ejecutar Nginx
CMD ["nginx", "-g", "daemon off;"]
