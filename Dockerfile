FROM busybox

MAINTAINER Kévin Labesse kevin@labesse.me

COPY . /var/www

RUN mkdir -p /var/www/var/cache/prod && chmod -R 777 /var/www/var/cache/prod

VOLUME /var/www
