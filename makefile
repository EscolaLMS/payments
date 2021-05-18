up:
	- cd env && docker-compose up -d
down:
	- cd env && docker-compose down
bash-php74: up
	- cd env && docker-compose exec escola_lms_app74 bash
test-php74: up
	- cd env && docker-compose exec escola_lms_app74 bash -c 'cp env/mysql/* .'
	- cd env && docker-compose exec escola_lms_app74 composer update
	- cd env && docker-compose exec escola_lms_app74 vendor/bin/testbench config:clear
	- cd env && docker-compose exec escola_lms_app74 vendor/bin/testbench migrate:fresh
	- cd env && docker-compose exec escola_lms_app74 vendor/bin/phpunit
test-php74-bash: test-php74
	- cd env && docker-compose exec escola_lms_app74 bash
bash-php80: up
	- cd env && docker-compose exec escola_lms_app80 bash
test-php80: up
	- cd env && docker-compose exec escola_lms_app80 bash -c 'cp env/mysql/* .'
	- cd env && docker-compose exec escola_lms_app80 composer update
	- cd env && docker-compose exec escola_lms_app80 vendor/bin/testbench config:clear
	- cd env && docker-compose exec escola_lms_app80 vendor/bin/testbench migrate:fresh
	- cd env && docker-compose exec escola_lms_app80 vendor/bin/phpunit
test-php80-bash: test-php80
	- cd env && docker-compose exec escola_lms_app80 bash
