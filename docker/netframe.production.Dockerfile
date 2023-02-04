FROM node:10-stretch AS assets-builder

WORKDIR /build
COPY . /build
RUN npx npm@5.6.0 install
#RUN ./node_modules/.bin/gulp
RUN npx npm@5.6.0 run production
RUN rm -rf node_modules/ docker/


FROM php:7.4.12-fpm-buster

VOLUME /var/www/html/storage/uploads

# Configure PHP
RUN mv "${PHP_INI_DIR}/php.ini-production" "${PHP_INI_DIR}/php.ini"
RUN sed -i 's/;fastcgi.logging/fastcgi.logging/' "${PHP_INI_DIR}/php.ini"
RUN sed -i 's/^memory_limit.*$/memory_limit = 2G/' "${PHP_INI_DIR}/php.ini"
RUN sed -i 's#;opcache.file_cache=.*$#opcache.file_cache = /tmp/opcache#' "${PHP_INI_DIR}/php.ini"
RUN sed -i 's/;opcache.max_accelerated_files=.*$/opcache.max_accelerated_files = 1000000/' "${PHP_INI_DIR}/php.ini"
# set posts/uploads limits, configured in the Nginx ConfigMap too
RUN sed -i 's/^upload_max_filesize.*$/upload_max_filesize = 1024M/' "${PHP_INI_DIR}/php.ini"
RUN sed -i 's/^max_file_uploads.*$/max_file_uploads = 40/' "${PHP_INI_DIR}/php.ini"
RUN sed -i 's/^post_max_size.*$/post_max_size = 1040M/' "${PHP_INI_DIR}/php.ini"
# set requests timeout, configured in the Nginx ConfigMap too
RUN sed -i 's/^max_input_time.*$/max_input_time = 3600/' "${PHP_INI_DIR}/php.ini"

# install required dependencies
RUN apt update
RUN mkdir -p /usr/share/man/man1
RUN apt install -y locales imagemagick default-jre libreoffice
RUN apt clean -y

# set locale to UTF-8 (for emojis)
RUN echo "en_US.UTF-8 UTF-8" > /etc/locale.gen
RUN locale-gen en_US.UTF-8
RUN dpkg-reconfigure locales
ENV LANG=en_US.UTF-8
ENV LANGUAGE=en_US:en
ENV LC_ALL=en_US.UTF-8
RUN /usr/sbin/update-locale

# install composer
# to select composer-setup version, use commits on https://github.com/composer/getcomposer.org/commits/master
ENV composer_version="c5e3f5a2a8e6742d38a9eb716161c32931243f57"
ADD https://raw.githubusercontent.com/composer/getcomposer.org/${composer_version}/web/installer /tmp/composer-setup.php
RUN php /tmp/composer-setup.php --version=1.8.4 --install-dir=/bin --filename=composer
RUN rm /tmp/composer-setup.php

# install required PHP extensions
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin
RUN chmod +x /usr/local/bin/install-php-extensions
RUN install-php-extensions openssl pdo pdo_mysql mbstring tokenizer xml zip bz2 imagick exif
RUN pecl install xdebug-2.9.8
RUN echo "zend_extension=/usr/local/lib/php/extensions/no-debug-non-zts-20190902/xdebug.so" >> /usr/local/etc/php/php.ini
RUN sed -i 's#<policy domain="coder" rights="none" pattern="PDF" />#<policy domain="coder" rights="read|write" pattern="PDF" />#' /etc/ImageMagick-6/policy.xml

# install opcache
RUN docker-php-ext-configure opcache --enable-opcache
RUN docker-php-ext-install opcache
# we should have a look at https://codelike.pro/preloading-laravel-php-7-4/

# install Laravel
RUN php /bin/composer global require laravel/installer

# PhantomJS needs to have a valid SSL conf directory
ENV OPENSSL_CONF=/etc/ssl/

# add the sources and install composer dependencies
RUN mkdir /app
RUN touch /app/.env
WORKDIR /app
# /app need to be moved to /var/www/html at startup
COPY --from=assets-builder /build /app/
RUN mkdir -m 777 -p storage/framework/cache storage/framework/sessions storage/framework/views
RUN MAIL_DRIVER=log SEARCH_ENABLED="false" SEARCH_HOSTS="http://localhost:9200" \
  php /bin/composer install
RUN chown -R www-data:www-data ./

# environment variables with default values
ENV APP_NAME="Netframe" \
    APP_ENV="production" \
    APP_DEBUG="false" \
    APP_LOG="errorlog" \
    APP_LOG_LEVEL="debug" \
    APP_URL="https://work.netframe.co" \
    APP_BASE_DOMAIN="netframe.co" \
    APP_BASE_PROTOCOL="https" \
    BROADCAST_DRIVER="redis" \
    CACHE_DRIVER="array" \
    DB_CONNECTION="mysql" \
    SESSION_DOMAIN=".netframe.co" \
    SESSION_DRIVER="file" \
    QUEUE_DRIVER="sync" \
    SEARCH_ENABLED="true" \
    MAIL_DRIVER="smtp" \
    MAIL_HOST="localhost" \
    MAIL_PORT="25"
# @TODO change SESSION_DRIVER for a scalable thing (redis?)

# environment variable without default values
ENV APP_KEY= \
    DB_HOST= \
    DB_PORT= \
    DB_DATABASE= \
    DB_USERNAME= \
    DB_PASSWORD= \
    REDIS_HOST= \
    REDIS_PASSWORD= \
    REDIS_PORT= \
    MAIL_USERNAME= \
    MAIL_PASSWORD= \
    MAIL_ENCRYPTION= \
    MAIL_FROM_ADDRESS= \
    MAIL_FROM_NAME= \
    SEARCH_HOSTS= \
    SEARCH_INDEX_PREFIX=
# environment variables for external services
ENV STRIPE_PUB_KEY= \
    STRIPE_SECRET_KEY= \
    PUSHER_APP_ID= \
    PUSHER_APP_KEY= \
    PUSHER_APP_SECRET= \
    GOOGLE_CLIENT_ID= \
    GOOGLE_CLIENT_SECRET= \
    GOOGLE_ACCESS_TYPE= \
    TENANT_ID= \
    ONEDRIVE_CLIENT_ID= \
    ONEDRIVE_CLIENT_SECRET= \
    DROPBOX_CLIENT_ID= \
    DROPBOX_CLIENT_SECRET= \
    BOX_CLIENT_ID= \
    BOX_CLIENT_SECRET=
