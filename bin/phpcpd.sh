#!/usr/bin/env bash
echo "Running the phpcpd"
docker-compose exec wordpress \
	phpcpd --exclude vendor --exclude tests .
	$*