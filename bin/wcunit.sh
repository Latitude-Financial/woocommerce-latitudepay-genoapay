#!/usr/bin/env bash
echo "Running the unit tests..."
docker-compose exec -w /var/www/html/wp-content/plugins/woocommerce/ wordpress \
	phpunit \
	$*