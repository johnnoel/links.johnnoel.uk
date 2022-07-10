FROM php:8.1-fpm-alpine
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN apk add nodejs && \
    chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions pdo_pgsql intl zip xsl @composer && \
    echo "date.timezone = Europe/London" >> /usr/local/etc/php/conf.d/timezone.ini && \
    echo "error_reporting = E_ALL" >> /usr/local/etc/php/conf.d/errors.ini && \
    echo "display_errors = On" >> /usr/local/etc/php/conf.d/errors.ini && \
    echo "display_startup_errors = On" >> /usr/local/etc/php/conf.d/errors.ini && \
    echo "upload_max_filesize = 64M" >> /usr/local/etc/php/conf.d/upload.ini && \
    echo "post_max_size = 64M" >> /usr/local/etc/php/conf.d/post.ini
WORKDIR /project
