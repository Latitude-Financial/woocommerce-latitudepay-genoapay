
#!/usr/bin/env bash
echo "Running the functional tests"
# docker-compose exec wordpress \
# 	mysqldump -uwoocommerce -pwoocommerce -hdb --no-tablespaces --databases woocommerce > ./tests/_data/dump.sql
docker-compose exec wordpress \
	rm -rf log/*.log && rm -rf tests/_output/*.html && rm -rf tests/_output/*.png && rm -rf tests/_output/failed
	$*
docker-compose exec wordpress \
	codecept run -c codeception.dev.yml functional --steps
	$*