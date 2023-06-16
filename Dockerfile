#syntax=docker/dockerfile:1.4

# Builder images
FROM composer/composer:latest-bin AS composer

# Database image
FROM postgres:${POSTGRES_VERSION:-15}-alpine as app_database

COPY docker/database/load-extensions.sh /docker-entrypoint-initdb.d/

# Production node image. Used to build frontend app
FROM node:slim AS app_node

ENV APP_ENV=prod
WORKDIR /srv/app

# Update npm if possible
RUN npm install -g npm

# Allow caching by installing npm modules before copying sources
COPY --link package*.json ./
RUN set -eux; npm ci --include-dev --no-audit --no-progress; \
	npm cache clean --force

# Copy sources
COPY --link  . ./

# Build
RUN npm run build

CMD ["echo", "Build complete. Exiting..."]

# Production php image
FROM php:8.2-fpm-alpine AS app_php

# Allow to use development versions of Symfony
ARG STABILITY="stable"
ENV STABILITY ${STABILITY}

# Allow to select Symfony version
ARG SYMFONY_VERSION=""
ENV SYMFONY_VERSION ${SYMFONY_VERSION}

ENV APP_ENV=prod
ENV APP_DEBUG=false
WORKDIR /srv/app

# Install install-php-extensions (https://github.com/mlocati/docker-php-extension-installer)
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions \
	/usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions

# Persistent / runtime deps
RUN apk add --no-cache \
	acl \
	fcgi \
	file \
	gettext \
	git \
	rabbitmq-c-dev \
	supervisor

RUN set -eux; \
	install-php-extensions \
	apcu \
	intl \
	opcache \
	zip \
	pdo \
	amqp \
	redis \
	gd

###> recipes ###
###> doctrine/doctrine-bundle ###
RUN apk add --no-cache --virtual .pgsql-deps postgresql-dev; \
	docker-php-ext-install -j"$(nproc)" pdo_pgsql; \
	apk add --no-cache --virtual .pgsql-rundeps so:libpq.so.5; \
	apk del .pgsql-deps
###< doctrine/doctrine-bundle ###
###< recipes ###

# Run supervisord to consume messages
COPY --link docker/php/supervisord/supervisord.conf /usr/local/etc/supervisord/supervisord.conf
RUN chmod +x /usr/local/etc/supervisord/supervisord.conf

# Copy php config
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY --link docker/php/conf.d/app.ini $PHP_INI_DIR/conf.d/
COPY --link docker/php/conf.d/app.prod.ini $PHP_INI_DIR/conf.d/

# Copy php-fpm config
COPY --link docker/php/php-fpm.d/zz-docker.conf /usr/local/etc/php-fpm.d/zz-docker.conf
RUN mkdir -p /var/run/php

# Copy php healthcheck
COPY --link docker/php/docker-healthcheck.sh /usr/local/bin/docker-healthcheck
RUN chmod +x /usr/local/bin/docker-healthcheck

HEALTHCHECK --interval=10s --timeout=3s --retries=3 CMD ["docker-healthcheck"]

# Copy entrypoint
COPY --link docker/php/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

ENTRYPOINT ["docker-entrypoint"]
CMD ["php-fpm"]

# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV PATH="${PATH}:/root/.composer/vendor/bin"

# Install composer (https://hub.docker.com/_/composer)
COPY --from=composer --link /composer /usr/bin/composer

# Allow caching by installing composer vendor before copying sources
COPY --link composer.* symfony.* ./
RUN set -eux; \
	if [ -f composer.json ]; then \
	composer install --no-dev --no-autoloader --no-scripts --no-progress; \
	composer clear-cache; \
	fi

# Copy sources
COPY --link  . ./
RUN rm -rf docker/

# Copy built frontend app
RUN rm -rf public/build
COPY --from=app_node --link /srv/app/public/build public/build

# Other composer things
RUN set -eux; \
	mkdir -p var/cache var/log;

RUN set -eux; \
	if [ -f composer.json ]; then \
	composer dump-autoload --classmap-authoritative --no-dev; \
	composer dump-env prod; \
	composer run-script --no-dev post-install-cmd; \
	chmod +x bin/console; sync; \
	fi

RUN set -eux; \
	mkdir -p public/uploads

# Development php image
FROM app_php AS app_php_dev

ENV APP_ENV=dev
ENV APP_DEBUG=true
ENV XDEBUG_MODE=coverage
VOLUME /srv/app/var/

# Persistent / runtime deps
RUN set -eux; \
	install-php-extensions \
	xdebug \
	;

# Delete production php config and install deveopment php config
RUN rm "$PHP_INI_DIR/conf.d/app.prod.ini"; \
	mv "$PHP_INI_DIR/php.ini" "$PHP_INI_DIR/php.ini-production"; \
	mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

COPY --link docker/php/conf.d/app.dev.ini $PHP_INI_DIR/conf.d/

RUN rm -f .env.local.php

# Caddy image
FROM caddy:latest AS app_caddy

WORKDIR /srv/app

COPY --from=app_php --link /srv/app/public public/
COPY --link docker/caddy/Caddyfile /etc/caddy/Caddyfile
