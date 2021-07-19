#!/usr/bin/env bash
echo "Running the unit tests..."
docker-compose exec wordpress \
	rm -rf log/*.log && rm -rf tests/_output/*.html && rm -rf tests/_output/*.png && rm -rf tests/_output/failed
	$*
docker-compose exec wordpress \
	codecept run -c codeception.dev.yml wpunit --coverage --steps
	$*