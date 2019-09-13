FROM php:fpm-alpine

COPY fpm.conf /usr/local/etc/php-fpm.d/
COPY supervisord.conf /etc/supervisord.conf
COPY php.ini /usr/local/etc/php/

RUN docker-php-source extract && \
    pecl bundle -d /usr/src/php/ext redis && rm /usr/src/php/ext/redis-*.tgz && \
    apk add --update --no-cache ca-certificates git zlib-dev supervisor bash && \
    apk add --no-cache --virtual .phpize-deps $PHPIZE_DEPS && \
    pecl install redis && \
    docker-php-ext-enable redis && \
    docker-php-ext-install zip pdo_mysql mysqli pcntl redis opcache && \
    mkdir /etc/supervisor.d && mkdir /var/log/supervisord && chmod -R 777 /var/log/supervisord && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer && \
    docker-php-source delete && \
    rm -rf /tmp/* && rm -rf /var/cache/apk/*

VOLUME /var/log/supervisord

CMD supervisord -c /etc/supervisord.conf

EXPOSE 9000

