FROM busybox

MAINTAINER Kévin Labesse kevin@labesse.me

COPY . /var/www

RUN chmod -R 777 /var/www/var/cache /var/www/var/logs

VOLUME /var/www
