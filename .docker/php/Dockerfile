ARG PHP_VERSION_SET_IMAGE

FROM $PHP_VERSION_SET_IMAGE

LABEL maintainer="Osiozekhai Aliu"

ARG WORKDIR_SERVER
ARG PHP_VERSION_SET
ARG XDEBUG_ENABLE
ARG TZ
ARG USER

COPY conf/zz-docker.conf /usr/local/etc/php-fpm.d/
COPY conf/xdebug.ini /usr/local/etc/php/conf.d/

RUN apk update && apk upgrade --available \
    && apk add --virtual build-dependencies \
        libc-dev \
        libxslt-dev \
        freetype-dev \
        libjpeg-turbo-dev \
        libpng \
        libpng-dev \
        libzip-dev \
    && set -xe \
    && apk add \
        git \
        bash \
        bash-completion \
        shadow \
	    nano \
        tzdata \
        icu \
        imagemagick-dev \
        imagemagick \
        bc \
        openssl \
    && apk add --virtual .php-deps \
        make \
    && apk add --virtual .build-deps \
        $PHPIZE_DEPS \
        zlib-dev \
        icu-dev \
        g++


RUN docker-php-ext-configure gd --with-freetype=/usr/include/freetype2 --with-jpeg=/usr/lib/x86_64-linux-gnu/ \
    && docker-php-ext-configure hash --with-mhash \
    && docker-php-ext-install \
        bcmath \
        mysqli \
        gd \
        intl \
        pdo_mysql \
        opcache \
        xsl \
        zip \
        soap \
    && ln -snf /usr/share/zoneinfo/$TZ /etc/localtime \
    && echo $TZ > /etc/timezone

RUN sed -i "s#__user#${USER}#g" /usr/local/etc/php-fpm.d/zz-docker.conf \
    && addgroup -g 1000 -S ${USER} \
    && adduser -u 1000 -S -D -G ${USER} ${USER} \
    && mkdir $WORKDIR_SERVER \
    && apk del tzdata \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && chmod +x /usr/local/bin/composer \
    && chown -R ${USER}:${USER} /home/${USER} \
    && chmod 775 $WORKDIR_SERVER

RUN apk add yarn nodejs npm
RUN rm -rf /var/cache/apk/* \
    && rm -rf /tmp/pear