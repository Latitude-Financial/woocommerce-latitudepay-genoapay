#!/usr/bin/env bash
set -e
echo "Installing the test environment..."

docker-compose exec wordpress \
	/bin/app/woocommerce/install.sh

echo "Install composer packages"
docker-compose exec wordpress rm -rf composer.lock
docker-compose exec wordpress rm -rf vendor/

echo "Install composer packages"

docker-compose exec wordpress composer install --ignore-platform-reqs