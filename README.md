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

> ⚠️ Erreur lors de la migration (ou de l’import du dump) :  
> `SQLSTATE[42501]: Insufficient privilege: 7 ERROR: permission denied for schema public`
>
> 💡 Solution : Connectez-vous sur la db `mpb` en tant qu’utilisateur `mpb` (ou `postgres`), puis exécutez les commandes suivantes :
>
> ```bash
> # Connexion à la base
> psql -U <user> -d mpb
> ```
>
> ```sql
> GRANT ALL ON SCHEMA public TO mpb;
> GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA public TO mpb;
> ```

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

> ⚠️ Erreur lors de la migration (ou de l’import du dump) :  
> `SQLSTATE[42501]: Insufficient privilege: 7 ERROR: permission denied for schema public`
>
> 💡 Solution : Connectez-vous sur la db `mpb_test` en tant qu’utilisateur `mpb` (ou `postgres`), puis exécutez les commandes suivantes :
>
> ```bash
> # Connexion à la base
> psql -U <user> -d mpb_test
> ```
>
> ```sql
> GRANT ALL ON SCHEMA public TO mpb;
> GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA public TO mpb;
> ```

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
