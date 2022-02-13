FROM wordpress:apache

RUN pecl install xdebug \
  && docker-php-ext-enable xdebug

# sendmail
RUN apt-get update && apt-get install -y --no-install-recommends \
  exim4-daemon-light \
  mailutils \
  msmtp \
  && apt-get clean && rm -rf /var/lib/apt/lists/*
COPY ./configs/sendmail.ini /usr/local/etc/php/conf.d/
COPY ./configs/update-exim4.conf.conf /etc/exim4/update-exim4.conf.conf
RUN service exim4 reload

WORKDIR /var/www/html
COPY docker-entrypoint.sh ./docker-entrypoint.sh

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["apache2-foreground"]
