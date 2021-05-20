up:
	- docker-compose -f env/docker-compose.yml up -d
down:
	- docker-compose -f env/docker-compose.yml down
bash-php74-mysql: up
	- docker-compose -f env/docker-compose.yml exec -u devilbox escola_lms_app74 bash
test-php74-mysql: up
	- [[ -d vendor ]] && sleep 2 #FIXME: properly wait for mysql
	- docker-compose -f env/docker-compose.yml exec -u devilbox escola_lms_app74 bash -c 'cp -rT env/mysql .'
	- docker-compose -f env/docker-compose.yml exec -u devilbox escola_lms_app74 composer update
	- docker-compose -f env/docker-compose.yml exec -u devilbox escola_lms_app74 vendor/bin/testbench config:clear
	- docker-compose -f env/docker-compose.yml exec -u devilbox escola_lms_app74 vendor/bin/testbench migrate:fresh
	- docker-compose -f env/docker-compose.yml exec -u devilbox escola_lms_app74 vendor/bin/phpunit
test-php74-mysql-bash: test-php74-mysql
	- docker-compose -f env/docker-compose.yml exec -u devilbox escola_lms_app74 bash
bash-php80-mysql: up
	- docker-compose -f env/docker-compose.yml exec -u devilbox escola_lms_app80 bash
test-php80-mysql: up
	- [[ -d vendor ]] && sleep 2 #FIXME: properly wait for mysql
	- docker-compose -f env/docker-compose.yml exec -u devilbox escola_lms_app80 bash -c 'cp -rT env/mysql .'
	- docker-compose -f env/docker-compose.yml exec -u devilbox escola_lms_app80 composer update
	- docker-compose -f env/docker-compose.yml exec -u devilbox escola_lms_app80 vendor/bin/testbench config:clear
	- docker-compose -f env/docker-compose.yml exec -u devilbox escola_lms_app80 vendor/bin/testbench migrate:fresh
	- docker-compose -f env/docker-compose.yml exec -u devilbox escola_lms_app80 vendor/bin/phpunit
test-php80-mysql-bash: test-php80-mysql
	- docker-compose -f env/docker-compose.yml exec -u devilbox escola_lms_app80 bash
