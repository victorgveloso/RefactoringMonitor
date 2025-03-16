FROM php:8-apache
RUN apt-get update && apt-get install -y sqlite3 libsqlite3-dev && rm -rf /var/lib/apt/lists/*
USER 1000:1000
COPY libsqlite_hashes.so /var/www/extensions/
COPY php.ini /usr/local/etc/php/conf.d/php.ini
COPY src/ /var/www/html/
