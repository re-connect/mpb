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
CREATE DATABASE mpb_db;
GRANT ALL PRIVILEGES ON DATABASE "mpb_db" to mpb;
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
CREATE DATABASE mpb_db_test;
GRANT ALL PRIVILEGES ON DATABASE "mpb_db_test" TO mpb;
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

---

## Frequent issues

**1.** `SQLSTATE[42501]: Insufficient privilege: 7 ERROR: permission denied for schema public`

This issue often occurs during Doctrine migrations or when importing a SQL dump.
It means that the user configured in `.env` doesn't have sufficient privileges on the public schema.

**Solution :**

1. Connect to the database :
   ```bash
   psql -U <user> -d <database>
   ```

2. Execute the following commands :
   ```sql
   GRANT ALL ON SCHEMA public TO mpb;
   GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA public TO mpb;
   ```

3. Rerun the migration :
   ```bash
   symfony console doctrine:migrations:migrate
   ```

---
