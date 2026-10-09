FROM nginx:alpine

# Nginx serve i file statici da questa directory.
COPY index.html /usr/share/nginx/html/index.html
RUN chmod 644 /usr/share/nginx/html/index.html

EXPOSE 80
