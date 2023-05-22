# Executables (local)
DOCKER_COMP = docker-compose

# Docker containers
PHP_CONT = $(DOCKER_COMP) exec php

# Executables
PHP      = $(PHP_CONT) php
COMPOSER = $(PHP_CONT) composer
SYMFONY  = $(PHP_CONT) bin/console

# Misc
.DEFAULT_GOAL = help
.PHONY        : help build up start down logs sh composer vendor sf cc lint-backend lint-fix-backend lint lint-fix

## —— The Symfony Docker Makefile ——————————————————————————————————————————————
help: ## Output this help screen
	@grep -E '(^[a-zA-Z0-9\./_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m\n/'

## —— Docker ———————————————————————————————————————————————————————————————————
build: ## Build the Docker images
	@$(DOCKER_COMP) build --pull --no-cache

up: ## Start the docker hub in detached mode (no logs)
	@$(DOCKER_COMP) up --detach

start: build up ## Build and start the containers

down: ## Stop the docker hub
	@$(DOCKER_COMP) down --remove-orphans

logs: ## Show live logs
	@$(DOCKER_COMP) logs --tail=0 --follow

php-sh: ## Connect to the PHP FPM container
	@$(PHP_CONT) sh

## —— Composer —————————————————————————————————————————————————————————————————
composer: ## Run composer, pass the parameter "c=" to run a given command, example: make composer c='req symfony/orm-pack'
	@$(eval c ?=)
	@$(COMPOSER) $(c)

vendor: ## Install vendors according to the current composer.lock file
vendor: c=install --prefer-dist --no-dev --no-progress --no-scripts --no-interaction
vendor: composer

## —— Symfony ——————————————————————————————————————————————————————————————————
sf: ## List all Symfony commands or pass the parameter "c=" to run a given command, example: make sf c=about
	@$(eval c ?=)
	@$(SYMFONY) $(c)

## —— Lint —————————————————————————————————————————————————————————————————————
lint: ## Lint the project
	@$(COMPOSER) run php-cs-fixer-dry

lint-fix: ## Fix lint issues in the project
	@$(COMPOSER) run  php-cs-fixer

## —— Test —————————————————————————————————————————————————————————————————————
test-backend: ## Test backend
	- @$(SYMFONY) doctrine:database:drop --force --env=test
	@$(SYMFONY) doctrine:database:create --env=test
	@$(SYMFONY) doctrine:migrations:migrate --no-interaction --env=test
	@$(SYMFONY) doctrine:fixtures:load --no-interaction --env=test --group=test
	@$(PHP_CONT) bin/phpunit --coverage-text --coverage-html var/coverage/

## —————————————————————————————————————————————————————————————————————————————
cc: c=c:c ## Clear the cache
cc: sf

load-fixtures:
	@$(SYMFONY) doctrine:fixtures:load --no-interaction --group=dev
