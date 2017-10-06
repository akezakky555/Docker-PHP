FROM ubuntu:16.04

RUN apt-get update && apt-get install -y curl build-essential php-dev php-curl libphp-embed php-fpm php-pgsql supervisor \
    && curl -O http://nginx.org/keys/nginx_signing.key \
    && apt-key add nginx_signing.key \
    && echo "deb http://nginx.org/packages/mainline/ubuntu/ xenial nginx" >> /etc/apt/sources.list \
    && echo "deb-src http://nginx.org/packages/mainline/ubuntu/ xenial nginx" >> /etc/apt/sources.list \
    && apt-get update && apt-get install -y nginx
ADD apps /var/www/apps
ADD conf/nginx/nginx.conf /etc/nginx/nginx.conf
ADD conf/nginx/ssl /etc/nginx/ssl
ADD conf/nginx/vhost /etc/nginx/vhost
RUN rm /etc/nginx/conf.d/default.conf
#PHP-FPM Config
ADD conf/php-fpm/www.conf /etc/php/7.0/fpm/pool.d/www.conf 

#SuperVisord Config
ADD conf/supervisor/supervisord.conf /etc/supervisor/supervisord.conf
RUN service php7.0-fpm start
WORKDIR /var/www

EXPOSE 80
EXPOSE 443

CMD ["/usr/bin/supervisord", "-n"]
