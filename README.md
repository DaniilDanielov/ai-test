# Rick and Morty Reviews
### Работа с API ключами
Приложение использует разные реализации оценки _SENTIMENT ANALYSIS_ в зависимости от переданного текста

Для оценки других языков нужно создать ключ и подставить его в .env
SENTIMENT_ANALYSIS_API_KEY (Дефолтный провайдер) =>  https://huggingface.co/settings/tokens

NLP_SENTIMENT_ANALYSIS_API_KEY (Провайдер английского текста) => https://nlpcloud.com/home/token
Ключ актуален (должен работать сразу) - оценка английских текстов будет менять общую оценку

## Установка и запуск

### Способ 1: С использованием Makefile (рекомендуется)

а) с Makefile
```bash
make envs
```

```bash
make init
```

### Способ 2: без Makefile
#### Копируем env
```bash
test -f .env || cp .env.example .env && \
test -f ./php/.env || cp ./php/.env.dev ./php/.env
```

#### Подставляем envs в init файл Postgres
```bash
set -a && . ./.env && set +a && cp -f ./docker/postgres/sql-template/100.sql ./docker/postgres/sql-dist/100.sql &&\
	envsubst < ./docker/postgres/sql-template/100.sql > ./docker/postgres/sql-dist/100.sql
  ```

#### Build контейнеров
```bash
docker-compose build
```

#### Поднятие контейнеров
```bash
docker-compose up -d
```

#### Установка composer
```bash
docker-compose exec -it symfony_app sh -c "cd /var/www/app/php && composer install  --prefer-source --no-interaction"
```

#### Миграции и тесты
```bash
docker-compose exec -it symfony_app sh -c "cd /var/www/app/php && php bin/console doctrine:migrations:migrate --no-interaction && ./vendor/bin/phpunit"
```


После запуска сервис будет доступен по адресу:  
**http://localhost:8080**

## Текст задания:
Create a service that meets the following requirements:

### Functional Requirements
- A user should be able to submit a review for a **Rick and Morty** episode (you can get the episode list from the [public API](https://rickandmortyapi.com/documentation/#get-all-episodes)).
- Each review will be automatically rated (on a scale from 0 to 1) using **sentiment analysis**.
- A user can request an episode's summary, which includes:
    - the episode name;
    - release date;
    - average sentiment score;
    - last 3 reviews for that episode.

### Non-Functional Requirements
- The service should be accessible via an HTTP API.
- It should be built using **PHP** and **Symfony** _(recommended)_ or **Laravel**.
- The database can be either **MySQL**/**MariaDB** or **PostgreSQL**.
- The application should be free of bugs and function smoothly.
- It should be set up for containerization and be ready to run locally using Docker Compose.
- _(Optional, but recommended)_ Include tests to ensure the service works correctly.

## Hints
- You don't need to implement sentiment analysis from scratch. Feel free to use an existing API or open-source library for that.
- Identity and access management isn't a focus for this task, so you can skip authentication or implement a simple solution.






