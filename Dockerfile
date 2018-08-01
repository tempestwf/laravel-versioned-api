FROM registry.sweetspotmotion.com/ssm/nginx-php-fpm:1-07
COPY nginx.config /etc/nginx/nginx.conf

ENV HOME=/aki
RUN mkdir -p $HOME/api
COPY . $HOME/api
WORKDIR $HOME/api

USER root
RUN whoami
RUN chown -R www-data:www-data $HOME/api
RUN cd $HOME/api
#COPY .env.example .env
RUN ls -ls

RUN git submodule init
RUN git submodule update

USER www-data:www-data
RUN cd $HOME/api && composer install

RUN php artisan key:generate
RUN php artisan jwt:generate
RUN php artisan vendor:publish --tag="config"

RUN php artisan doctrine:migrations:migrate
RUN php artisan doctrine:generate:proxies

RUN php artisan db:seed
RUN php artisan db:seed --class=SampleRecordsSeeder

USER root

RUN mkdir /run/php

EXPOSE 80
CMD ["/usr/bin/supervisord", "--nodaemon", "-c", "/etc/supervisor/supervisord.conf"]