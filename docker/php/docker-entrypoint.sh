#!/bin/sh
set -e

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
    set -- php-fpm "$@"
fi

if [ "$1" = 'php-fpm' ] || [ "$1" = 'php' ] || [ "$1" = 'bin/console' ]; then
    if [ "$APP_ENV" != 'prod' ]; then
        composer install --no-progress --no-interaction
        mkdir -p public/uploads
    fi

    setfacl -R -m u:www-data:rwX -m u:"$(whoami)":rwX var public/uploads
    setfacl -dR -m u:www-data:rwX -m u:"$(whoami)":rwX var public/uploads

    if grep -q ^DATABASE_URL= .env; then
        echo "Waiting for db to be ready..."
        ATTEMPTS_LEFT_TO_REACH_DATABASE=60
        until [ $ATTEMPTS_LEFT_TO_REACH_DATABASE -eq 0 ] || DATABASE_ERROR=$(bin/console dbal:run-sql "SELECT 1" 2>&1); do
            if [ $? -eq 255 ]; then
                # If the Doctrine command exits with 255, an unrecoverable error occurred
                ATTEMPTS_LEFT_TO_REACH_DATABASE=0
                break
            fi
            sleep 1
            ATTEMPTS_LEFT_TO_REACH_DATABASE=$((ATTEMPTS_LEFT_TO_REACH_DATABASE - 1))
            echo "Still waiting for db to be ready... Or maybe the db is not reachable. $ATTEMPTS_LEFT_TO_REACH_DATABASE attempts left"
        done

        if [ $ATTEMPTS_LEFT_TO_REACH_DATABASE -eq 0 ]; then
            echo "The database is not up or not reachable:"
            echo "$DATABASE_ERROR"
            exit 1
        else
            echo "The db is now ready and reachable"
        fi

        if [ "$( find ./migrations -iname '*.php' -print -quit )" ]; then
            bin/console doctrine:migrations:migrate --no-interaction
        fi
    fi

    if grep -q ^RABBITMQ_URL= .env; then
        echo "Waiting for RabbitMQ to be ready..."
        ATTEMPTS_LEFT_TO_REACH_RABBITMQ=60
        until [ $ATTEMPTS_LEFT_TO_REACH_RABBITMQ -eq 0 ] || RABBITMQ_ERROR=$(bin/console messenger:setup-transports 2>&1); do
            sleep 1
            ATTEMPTS_LEFT_TO_REACH_RABBITMQ=$((ATTEMPTS_LEFT_TO_REACH_RABBITMQ - 1))
            echo "Still waiting for RabbitMQ to be ready... Or maybe the RabbitMQ is not reachable. $ATTEMPTS_LEFT_TO_REACH_RABBITMQ attempts left"
        done

        if [ $ATTEMPTS_LEFT_TO_REACH_RABBITMQ -eq 0 ]; then
            echo "The RabbitMQ is not up or not reachable:"
            echo "$RABBITMQ_ERROR"
            exit 1
        else
            echo "The RabbitMQ is now ready and reachable"
        fi

        echo "Starting supervisor service"
        supervisord --configuration /usr/local/etc/supervisord/supervisord.conf
    fi
fi

exec docker-php-entrypoint "$@"
