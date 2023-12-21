#!/usr/bin/env bash

docker run --rm --name=composer -w /app -v $(pwd):/app composer:2 composer install
docker run --rm --name=php -w /app -v $(pwd):/app php:8.3-cli php app.php input.txt
