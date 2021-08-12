SAIL=bash ./vendor/bin/sail

# Colors
GREEN=\033[0;32m
BOLD_GREEN=\033[1;32m

ADDRESS=sail artisan qanda:interactive

project: install
	$(SAIL) up -d
	cp .env.example .env
	$(SAIL) artisan optimize:clear
	$(SAIL) artisan key:generate
	$(SAIL) artisan migrate --seed
	@echo Run: "${BOLD_GREEN} ${COMMAND}"

install:
	docker run --rm \
	-u "$(id -u):$(id -g)" \
	-v $(pwd):/opt \
	-w /opt \
	laravelsail/php80-composer:latest \
	composer install --ignore-platform-reqs

dev:
	$(SAIL) up

dev-stop:
	$(SAIL) down

purge:
	$(SAIL) down -v
