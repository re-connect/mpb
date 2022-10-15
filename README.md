# Setup

## Requirements

* PHP 8.1
* Symfony client [https://symfony.com/downloads](https://symfony.com/downloads)
* Postgresql
* Npm

## Database

* Install Psql
* Connect to postgres `psql`
* Create a db, user, and grant privileges

```postgresql
CREATE USER mpb WITH PASSWORD 'mpb';
CREATE DATABASE mpb;
GRANT ALL PRIVILEGES ON DATABASE "mpb" to mpb;
```

* Run migrations

```bash
symfony console doctrine:migrations:migrate
```

## Run

```bash
symfony composer install
symfony serve
```

```bash
npm install
npm run dev 
```

## Tests

* Create test database

```postgresql
CREATE DATABASE mpb_test;
GRANT ALL PRIVILEGES ON DATABASE "mpb_test" TO mpb;
```

* Load fixtures

```bash
symfony console doctrine:migrations:migrate --env=test
symfony console doctrine:fixtures:load --env=test
```

* Use phpunit to run tests

```bash
php ./vendor/bin/simple-phpunit tests
```

## Deployment

```bash
php ./vendor/bin/dep deploy
```
