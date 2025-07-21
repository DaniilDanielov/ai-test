# crt-symfony-4
## 🛠 Установка и запуск

### Способ 1: С использованием Makefile (рекомендуется)

а) с Makefile
```bash
make envs
```

```bash
make init
```


### Способ 2: без Makefile
1) Скопировать .env.example и сохранить его как .env


После запуска сервис будет доступен по адресу:  
**http://localhost:8000**

## Система анализа тональности

Приложение использует разные реализации _Sentiment Analysis_ в зависимости от языка текста:

### Работа с API ключами
Приложение использует разные реализации оценки _SENTIMENT ANALYSIS_ в зависимости от переданного текста

NLP_SENTIMENT_ANALYSIS_API_KEY (Провайдер английского текста) => https://nlpcloud.com/home/token
Ключ актуален (должен работать сразу) - оценка английских текстов будет менять общую оценку


Для оценки других языков нужно создать ключи и подставить его в .env
SENTIMENT_ANALYSIS_API_KEY (Дефолтный провайдер) =>  https://huggingface.co/settings/tokens

Работает, через access keys, поэтому если оценка не будет изменяться (ключи истекли) нужно добавить свои ключи в .env

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






