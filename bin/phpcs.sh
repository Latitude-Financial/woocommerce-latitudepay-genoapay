#!/usr/bin/env bash
echo "Running the phpcs"
docker-compose exec wordpress \
	phpcs --config-set show_warnings 0
docker-compose exec wordpress \
	phpcs --standard=phpcs.xml.dist --extensions=php --ignore=*/vendor/,*/tests/ --colors -s -p -v ./
	$*