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
symfony console assets:install
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
# Or using makefile
make test
```

## Deployment

```bash
php ./vendor/bin/dep deploy
# Or using Make
make deploy
```

## Code quality standards
We use php-cs-fixer, rector, and phpstan
```bash
php ./vendor/bin/rector process
php ./vendor/bin/phpstan analyse
php ./vendor/bin/php-cs-fixer fix src --allow-risky=yes
php ./vendor/bin/php-cs-fixer fix tests --allow-risky=yes
# Or using Make
make rector
make stan
make lint
# Or running all at once
make cs
```
