SAIL=./vendor/bin/sail

# Colors
GREEN=\033[0;32m
BOLD_GREEN=\033[1;32m

COMMAND=sail artisan qanda:interactive

run: install start

install: vendor
	$(SAIL) up -d
	@echo "${BOLD_GREEN}We want to make sure all the services are up..."
	@#sleep 60 # wait for all services to start completely
	cp .env.example .env
	$(SAIL) artisan sail:install --with=mysql
	$(SAIL) artisan key:generate
	$(SAIL) artisan migrate --seed
	@echo Run: "${BOLD_GREEN}${COMMAND}"

vendor:
	docker run --rm -u "$(shell id -u):$(shell id -g)" \
	-v $(shell pwd):/opt \
	-w /opt \
	laravelsail/php80-composer:latest \
	composer install --ignore-platform-reqs

start:
	@echo "${BOLD_GREEN}Please wait..."
	@$(SAIL) artisan qanda:interactive

dev:
	$(SAIL) up

uninstall:
	$(SAIL) down

purge:
	$(SAIL) down -v --remove-orphans
