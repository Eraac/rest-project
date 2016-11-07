FROM busybox

MAINTAINER Kévin Labesse kevin@labesse.me

COPY . /var/www

RUN chown -R www-data:www-data /var/www/var/cache /var/www/var/logs /var/www/var/sessions

VOLUME /var/www
