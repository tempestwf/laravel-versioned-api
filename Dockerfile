FROM registry.sweetspotmotion.com:4567/sweetspotmotion/nginx-php-fpm:master
COPY nginx.config /etc/nginx/nginx.conf

RUN apt-get update
RUN apt-get install -y git

ENV HOME=/aki
RUN mkdir -p $HOME/api
COPY . $HOME/api
WORKDIR $HOME/api

USER root
RUN whoami
RUN chown -R www-data:www-data $HOME/api
RUN cd $HOME/api
COPY .env.example .env
RUN ls -ls

RUN git submodule init
RUN git submodule update

USER www-data:www-data
RUN cd $HOME/api && composer install

RUN php artisan key:generate
RUN php artisan jwt:generate