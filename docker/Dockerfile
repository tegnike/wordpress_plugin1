FROM wordpress:apache

RUN pecl install xdebug \
  && docker-php-ext-enable xdebug

WORKDIR /var/www/html
COPY docker-entrypoint.sh ./docker-entrypoint.sh
ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["apache2-foreground"]
