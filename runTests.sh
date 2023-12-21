#!/usr/bin/env bash

docker run --rm --name=php -w /app -v $(pwd):/app php:8.3-cli vendor/bin/phpunit tests