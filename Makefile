	-include .env
rebuild: docker-down docker-prune docker-rebuild
init: envs docker-build init-app du
du: docker-up
dd: docker-down
envs: create_envs create_initial_sql_files
restart: du du

test:
	grep -P '^\tinclude .env' Makefile || sed -i '1s/^/\tinclude .env\n/' Makefile

cdu:
	 docker-compose exec ${APP_CONTAINER} composer du || true

docker-down:
	docker-compose down --remove-orphans || true

docker-up:
	docker-compose up -d
	docker-compose exec -it symfony_app sh -c "cd /var/www/app/php && composer install  --prefer-source --no-interaction"
	@docker-compose exec -it symfony_app sh -c "cd /var/www/app/php && php bin/console doctrine:migrations:migrate --no-interaction && ./vendor/bin/phpunit"
	@docker-compose exec -it symfony_app sh -c "cd /var/www/app/php && php ./vendor/bin/phpunit"

docker-build:
	docker-compose build

docker-rebuild:
	docker-compose build --no-cache

docker-prune:
	docker-compose rm -fsv

php:
	docker-compose exec -it symfony_app sh

create_envs:
	@test -f .env || cp .env.example .env
	@chmod 644 .env
	@grep -P '^\t-include .env' Makefile || sed -i '1s/^/\t-include .env\n/' Makefile

create_initial_sql_files:
	set -a && . ./.env && set +a && cp -f ./docker/postgres/sql-template/100.sql ./docker/postgres/sql-dist/100.sql &&\
	envsubst < ./docker/postgres/sql-template/100.sql > ./docker/postgres/sql-dist/100.sql

init-app:
	@test -f ./php/.env || cp ./php/.env.dev ./php/.env

