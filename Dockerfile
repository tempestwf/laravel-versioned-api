FROM registry.sweetspotmotion.com:4567/sweetspotmotion/nginx-php-fpm:master
COPY nginx.config /etc/nginx/nginx.conf

ENV HOME=/aki
COPY . $HOME/api
RUN mkdir -p $HOME/api
WORKDIR $HOME/api

USER root
RUN whoami
RUN chown -R www-data:www-data $HOME/api
RUN cd $HOME/api
RUN ls -ls

RUN apt-get install git-core
RUN git submodule init
RUN git submodule update

USER www-data:www-data
RUN cd $HOME/api && composer install

RUN php artisan key:generate
RUN php artisan jwt:generate