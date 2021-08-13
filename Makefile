SAIL=bash ./vendor/bin/sail

# Colors
GREEN=\033[0;32m
BOLD_GREEN=\033[1;32m

COMMAND=sail artisan qanda:interactive

run: install migrate start

install: vendor
	$(SAIL) up -d
	cp .env.example .env
	#$(SAIL) artisan sail:install --with=mysql
	$(SAIL) artisan key:generate

migrate:
	$(SAIL) ps
	$(SAIL) artisan migrate:fresh --seed

start:
	@echo "${BOLD_GREEN}Please wait..."
	@$(SAIL) artisan qanda:interactive

purge:
	$(SAIL) down -v --remove-orphans

vendor:
	docker run --rm -u "$(shell id -u):$(shell id -g)" \
	-v $(shell pwd):/opt \
	-w /opt \
	laravelsail/php80-composer:latest \
	composer install --ignore-platform-reqs
