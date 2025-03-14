FROM php:8-apache
RUN apt-get update && apt-get install -y sqlite3 libsqlite3-dev && rm -rf /var/lib/apt/lists/*
USER 1000:1000
COPY src/ /var/www/html/
COPY ini/php.ini /usr/local/etc/php/conf.d/php.ini
