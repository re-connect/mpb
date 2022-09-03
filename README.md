# Setup

## Database

* Install Psql
* Connect to postgres `psql`
* Create a db, user, and grant privileges

```
CREATE USER mpb WITH PASSWORD 'mpb';
CREATE DATABASE mpb;
GRANT ALL PRIVILEGES ON DATABASE "mpb" to mpb;
CREATE DATABASE mpb_test;
GRANT ALL PRIVILEGES ON DATABASE "mpb_test" to mpb;
```

* Load fixtures

```
symfony console doctrine:migrations:migrate
symfony console doctrine:migrations:migrate --env=test
```

```
symfony console doctrine:fixtures:load --env=test
```