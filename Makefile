	include .env
build: docker-build docker-init
rebuild: docker-down docker-prune docker-rebuild docker-init
init: envs docker-init
du: docker-up cdu
dd: docker-down


test:
	grep -P '^\tinclude .env' Makefile || sed -i '1s/^/\tinclude .env\n/' Makefile

cdu:
	 docker-compose exec ${APP_CONTAINER} composer du || true

docker-down:
	docker-compose down --remove-orphans || true

docker-up:
	docker-compose up -d

docker-build:
	docker-compose build

docker-rebuild:
	docker-compose build --no-cache

docker-init:
	make dd
	make du

restart: docker-down docker-build docker-up

docker-prune:
	docker-compose rm -fsv

php:
	docker-compose exec -it symfony_app sh

# Инициализация переменных и их внесение в template файлы
envs: create_envs create_initial_sql_files

#Инициализация env файлов
create_envs:
	@test -f .env || cp .env.example .env
	@grep -P '^\tinclude .env' Makefile || sed -i '1s/^/\tinclude .env\n/' Makefile

#Сборка файлов для инициализации БД
create_initial_sql_files:
	set -a && . ./.env && set +a && cp -f ./docker/postgres/sql-template/100.sql ./docker/postgres/sql-dist/100.sql &&\
	envsubst < ./docker/postgres/sql-template/100.sql > ./docker/postgres/sql-dist/100.sql


