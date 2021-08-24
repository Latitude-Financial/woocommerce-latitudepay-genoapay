#!/usr/bin/env bash
echo "Running Backward Compatibility Check"
docker-compose exec wordpress \
	vendor/bin/roave-backward-compatibility-check
	$*