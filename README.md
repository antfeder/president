# Surveys

[Laravel Docs](https://laravel.com/docs/9.x)

## Init

```sh
./vendor/bin/sail up -d --build
./vendor/bin/sail php artisan key:generate
./vendor/bin/sail php artisan config:cache
./vendor/bin/sail artisan surveys:refresh
```

## If you want to run unit tests

```sh
./vendor/bin/sail test
```

Go to http://localhost

## Data refresh

### Using scheduler

For dev purpose, cron is scheduled to execute every minute, but i guess daily run would be fine.

To simulate locally:

```sh
sail php artisan schedule:work
```

### Manually

```sh
sail php artisan surveys:refresh
```

## Misc

### Cache exploration

Using redis commander at http://0.0.0.0:8081/

### Resolving mysql error 'caching_sha2_password'

```sh
docker-compose exec mysql bash

mysql -u root -p
password 

GRANT ALL PRIVILEGES ON *.* TO 'sail'@'%';
ALTER USER 'sail'@'%' IDENTIFIED WITH mysql_native_password BY 'password';
```
