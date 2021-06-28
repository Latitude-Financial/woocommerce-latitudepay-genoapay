#!/usr/bin/env bash
set -e

echo "Remove composer packages"
docker-compose exec wordpress rm -rf composer.lock
docker-compose exec wordpress rm -rf vendor/

echo "Install composer packages"

docker-compose exec wordpress composer install --ignore-platform-reqs

echo "Installing the woocommerce..."
docker-compose exec wordpress \
	/bin/app/woocommerce/install.sh