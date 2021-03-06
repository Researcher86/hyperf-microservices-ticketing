FROM hyperf/hyperf:8.0-alpine-v3.15-swoole-v4.8 as base
LABEL maintainer="Hyperf Developers <group@hyperf.io>" version="1.0" license="MIT" app.name="Hyperf"

##
# ---------- env settings ----------
##
# --build-arg timezone=Asia/Shanghai
ARG timezone

ENV TIMEZONE=${timezone:-"Asia/Shanghai"} \
    APP_ENV=prod \
    SCAN_CACHEABLE=(true)

# update
RUN set -ex \
#    && apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
#    && ln -s /usr/bin/pecl8 /usr/local/bin/pecl \
    # show php version and extensions
    && php -v \
    && php -m \
    && php --ri swoole \
#    && pecl install xdebug \
#    && echo "zend_extension=xdebug.so" > /etc/php8/conf.d/20_xdebug.ini \
    #  ---------- some config ----------
    && cd /etc/php8 \
    # - config PHP
    && { \
        echo "upload_max_filesize=128M"; \
        echo "post_max_size=128M"; \
        echo "memory_limit=1G"; \
        echo "date.timezone=${TIMEZONE}"; \
    } | tee conf.d/99_overrides.ini \
    # - config timezone
    && ln -sf /usr/share/zoneinfo/${TIMEZONE} /etc/localtime \
    && echo "${TIMEZONE}" > /etc/timezone \
    # ---------- clear works ----------
    && rm -rf /var/cache/apk/* /tmp/* /usr/share/man \
    && echo -e "\033[42;37m Build Completed :).\033[0m\n"

WORKDIR /opt/www

ENTRYPOINT ["php", "/opt/www/bin/hyperf.php", "start"]

FROM base as auth
EXPOSE 9501

FROM base as tickets
EXPOSE 9501

FROM base as orders
EXPOSE 9501

FROM base as expiration
EXPOSE 9501

FROM base as payments
EXPOSE 9501


FROM node:17-alpine as node-base
WORKDIR /opt/www

FROM node-base as client
EXPOSE 3000