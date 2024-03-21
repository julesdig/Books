FROM nginx:alpine

ARG USER
ARG TZ
ARG WORKDIR_SERVER
ARG SERVER_NAME
ARG SERVER_PATH

COPY conf/nginx.conf /etc/nginx/nginx.conf
COPY conf/default.conf /etc/nginx/conf.d/default.conf

# Copier les certificats depuis le répertoire actuel (où se trouve le Dockerfile)
COPY ssl/fullchain.pem /etc/nginx/ssl/fullchain.pem
COPY ssl/privkey.pem /etc/nginx/ssl/privkey.pem
COPY ssl/dhparam.pem /etc/nginx/dhparam.pem
RUN apk add --update openssl tzdata curl \
    && ln -sf /usr/share/zoneinfo/Etc/$TZ  /etc/localtime \
    && echo $TZ > /etc/timezone \
    && addgroup -g 1000 --system ${USER} \
    && adduser -u 1000 --system -D -G ${USER} ${USER} \
    && mkdir -p ${WORKDIR_SERVER} \
    && chown -R ${USER}:${USER} ${WORKDIR_SERVER} \
    && chmod -R 755 ${WORKDIR_SERVER} \
    && mkdir -p /etc/nginx/ssl \
    && sed -i "s#__user#${USER}#g" /etc/nginx/nginx.conf \
    && sed -i "s#__working_dir#${WORKDIR_SERVER}#g" /etc/nginx/conf.d/default.conf \
    && sed -i "s#__server_path#${SERVER_PATH}#g" /etc/nginx/conf.d/default.conf \
    && sed -i "s#__server_name#${SERVER_NAME}#g" /etc/nginx/conf.d/default.conf \
    && apk del tzdata \
    && rm -rf /var/cache/apk/*

RUN chown -R ${USER}:${USER} /etc/nginx/ssl/

