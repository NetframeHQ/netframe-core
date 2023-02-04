FROM php:8.1.1-fpm-buster

# PhantomJS needs to have a valid SSL conf directory
ENV OPENSSL_CONF=/etc/ssl/

# Configure PHP
RUN mv "${PHP_INI_DIR}/php.ini-development" "${PHP_INI_DIR}/php.ini"
RUN sed -i 's/;fastcgi.logging/fastcgi.logging/' /usr/local/etc/php/php.ini
RUN sed -i 's/^memory_limit.*$/memory_limit = -1/' /usr/local/etc/php/php.ini

# install required dependencies
RUN apt update
RUN mkdir -p /usr/share/man/man1
RUN apt install -y git netcat locales imagemagick default-jre libreoffice
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
# to select composer-setup version, use commits on https://github.com/composer/getcomposer.org/commits/main
ENV composer_version="526000b8958ea53546d29a8124217f60f26e3c1a"
ADD https://raw.githubusercontent.com/composer/getcomposer.org/${composer_version}/web/installer /tmp/composer-setup.php
RUN php /tmp/composer-setup.php --version=2.0.9 --install-dir=/bin --filename=composer
RUN rm /tmp/composer-setup.php

# install required PHP extensions
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin
RUN chmod +x /usr/local/bin/install-php-extensions
RUN install-php-extensions openssl pdo pdo_mysql mbstring tokenizer xml zip bz2 imagick exif
RUN pecl install xdebug-3.2.0
RUN echo "zend_extension=/usr/local/lib/php/extensions/no-debug-non-zts-20210902/xdebug.so" >> /usr/local/etc/php/php.ini
RUN sed -i 's#<policy domain="coder" rights="none" pattern="PDF" />#<policy domain="coder" rights="read|write" pattern="PDF" />#' /etc/ImageMagick-6/policy.xml

# install Laravel
RUN php /bin/composer global require laravel/installer

RUN mkdir -m 777 /app
VOLUME /app
WORKDIR /app
