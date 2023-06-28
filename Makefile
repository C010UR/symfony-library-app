# Executables (local)
DOCKER         = docker
DOCKER_COMP    = docker compose
NPM_LOCAL      = npm
COMPOSER_LOCAL = composer

# Docker containers
PHP_CONT = $(DOCKER_COMP) exec php

# Executables
PHP      = $(PHP_CONT) php
COMPOSER = $(PHP_CONT) composer
SYMFONY  = $(PHP_CONT) bin/console

# Misc
.DEFAULT_GOAL = help
.PHONY        : help build up start serve down logs php clean composer docker-vendor vendor symfony load-fixtures serve-load-fixtures lint format test install dev cc

## ——  Makefile ————————————————————————————————————————————————————————————————
help: ## Output this help message
	@grep -E '(^[a-zA-Z0-9\./_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m\n/'

## —— Docker ———————————————————————————————————————————————————————————————————
build: ## Build the Docker images
	@ $(DOCKER_COMP) build
#--pull --no-cache

up: ## Start the docker hub in detached mode (no logs)
	@ $(DOCKER_COMP) up --detach --wait

start: build up ## Build and start the containers for dev

serve: ## Build and start the containers in staging mode
	@ $(DOCKER_COMP) -f docker-compose.yml up --build --detach --wait --no-color

down: ## Stop the docker hub
	@ $(DOCKER_COMP) down --remove-orphans

logs: ## Show live logs
	@ $(DOCKER_COMP) logs --tail=0 --follow

php: ## Connect to the PHP FPM container
	@ $(PHP_CONT) sh

clean: ## Remove all volumes from docker
	-@ $(DOCKER) volume rm $(shell $(DOCKER) volume ls -q)

## —— Composer —————————————————————————————————————————————————————————————————
composer: ## Run composer, pass the parameter "c=" to run a given command, example: make composer c='req symfony/orm-pack'
	@ $(eval c ?=)
	@ $(COMPOSER) $(c)

docker-vendor: ## Install vendors according to the current composer.lock file
docker-vendor: c=install --prefer-dist --no-dev --no-progress --no-scripts --no-interaction
docker-vendor: composer

vendor: ## Install vendors according to the current composer.lock file
	@ $(COMPOSER_LOCAL) install --ignore-platform-reqs

## —— Symfony ——————————————————————————————————————————————————————————————————
symfony: ## List all Symfony commands or pass the parameter "c=" to run a given command, example: make symfony c=about
	@ $(eval c ?=)
	@ $(SYMFONY) $(c)

load-fixtures: ## Load fixutres
	@ $(SYMFONY) doctrine:fixtures:load --no-interaction --group=dev

## —— Lint —————————————————————————————————————————————————————————————————————
lint: ## Lint the project
	-@ $(COMPOSER) run php-cs-fixer-dry
	-@ $(COMPOSER) run rector-dry
	-@ $(NPM_LOCAL) run type-check
	-@ $(NPM_LOCAL) run lint

format: ## Fix lint issues in the project
	-@ $(COMPOSER) run php-cs-fixer
	-@ $(COMPOSER) run rector
	-@ $(NPM_LOCAL) run format

## —— Test —————————————————————————————————————————————————————————————————————
test: ## Run tests
	-@ $(SYMFONY) doctrine:database:drop --force --env=test
	@ $(SYMFONY) doctrine:database:create --env=test
	@ $(SYMFONY) doctrine:query:sql "CREATE EXTENSION IF NOT EXISTS citext" --env=test
	@ $(SYMFONY) doctrine:migrations:migrate --no-interaction --env=test
	@ $(SYMFONY) doctrine:fixtures:load --no-interaction --env=test --group=test
	@ $(PHP_CONT) bin/phpunit --coverage-text --coverage-html var/coverage/

## —— NPM ——————————————————————————————————————————————————————————————————————
install: ## Install dependencies according to the current composer.lock file
	@ $(NPM_LOCAL) install --include-dev

dev: ## Run vite dev
	@ $(NPM_LOCAL) run dev

## —————————————————————————————————————————————————————————————————————————————
cc: c=c:c ## Clear the cache
cc: symfony
