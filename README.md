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

```
CREATE USER mpb WITH PASSWORD 'mpb';
CREATE DATABASE mpb;
GRANT ALL PRIVILEGES ON DATABASE "mpb" to mpb;
```

* Run migrations

```
symfony console doctrine:migrations:migrate
```

## Run

```
symfony composer install
symfony serve
```
```
npm install
npm run dev 
```


## Tests

* Create test database
```
CREATE DATABASE mpb_test;
GRANT ALL PRIVILEGES ON DATABASE "mpb_test" to mpb;
```

* Load fixtures
```
symfony console doctrine:migrations:migrate --env=test
symfony console doctrine:fixtures:load --env=test
```
* Use phpunit to run tests
```
phpunit tests 
```