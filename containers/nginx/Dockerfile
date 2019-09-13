FROM nginx:alpine

#COPY default.conf /etc/nginx/conf.d

EXPOSE 80 443

CMD ["nginx", "-g", "daemon off;"]

#COPY cert.crt /etc/nginx/conf.d
#COPY private.key /etc/nginx/conf.d
#COPY dhparam.pem /etc/nginx/conf.d
#
#VOLUME ["/var/www/html", "/var/log/nginx"]