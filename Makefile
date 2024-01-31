ARGS=$(filter-out $@, $(MAKECMDGOALS))

up: .docker-up
init: .composer-install .migrate
rebuild: .docker-down .docker-build up init
console: .docker-console
fixtures: .fixtures

.docker-up:
	cd ./docker && docker-compose up -d

.docker-down:
	cd ./docker && docker-compose down

.docker-build:
	cd ./docker && docker-compose build

.docker-console:
	cd ./docker && docker-compose exec app ash

.create-env:
	if [ ! -f './docker/.env' ]; then cp ./docker/.env.dist ./docker/.env; else exit 0; fi;
	if [ ! -f './phpunit.xml' ]; then cp ./phpunit.xml.dist ./phpunit.xml; else exit 0; fi;

.composer-install:
	cd ./docker && docker-compose exec app composer install

.migrate:
	cd ./docker && docker-compose exec app bin/console doctrine:migrations:migrate --no-interaction

.fixtures:
	cd ./docker && docker-compose exec app ./bin/console doctrine:fixtures:load --no-interaction --no-debug
