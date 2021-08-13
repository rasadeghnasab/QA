SAIL=bash ./vendor/bin/sail

# Colors
GREEN=\033[0;32m
BOLD_GREEN=\033[1;32m

COMMAND=sail artisan qanda:interactive

run: install migrate

install: vendor
	$(SAIL) up -d
	cp .env.example .env
	$(SAIL) artisan sail:install --with=mysql
	$(SAIL) artisan key:generate
	@echo "Wait until all services up successfully"
	@echo "You can check the services status using ${BOLD_GREEN}make status"

migrate: status
	$(SAIL) artisan migrate:fresh --seed

status:
	$(SAIL) ps

down:
	$(SAIL) down -v --remove-orphans

purge:
	$(SAIL) down -v --remove-orphans
	rm -rf vendor

vendor:
	docker run --rm -u "$(shell id -u):$(shell id -g)" \
	-v $(shell pwd):/opt \
	-w /opt \
	laravelsail/php80-composer:latest \
	composer install --ignore-platform-reqs
