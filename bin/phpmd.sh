#!/usr/bin/env bash
echo "Running the phpmd"
docker-compose exec wordpress \
	phpmd ./ text phpmd.xml.dist --exclude vendor/,tests/
	$*