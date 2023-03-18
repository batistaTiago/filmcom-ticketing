## Useful commands

### Installing PHP libraries
```
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v $(pwd):/var/www/html \
    -w /var/www/html \
    laravelsail/php82-composer:latest \
    composer install --ignore-platform-reqs
```

```
docker run --rm -u "197609:197121" -v C:\Users\batista\Documents\Projetos\filmcom-ticketing:/var/www/html -w /var/www/html laravelsail/php82-composer:latest composer install --ignore-platform-reqs
```

### Running tests outside of the container
```
DB_HOST=localhost REDIS_HOST=localhost vendor/bin/phpunit
```

### Running tests with coverage
```
XDEBUG_MODE=coverage vendor/bin/phpunit --coverage-html='./tests/coverage_report'
```
