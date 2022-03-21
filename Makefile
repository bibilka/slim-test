build:
	cp .env.example .env
	php generate_secret_key.php
	docker-compose build
up:
	composer install
	docker-compose up -d
	docker-compose ps
migrate:
	composer install
	composer dump-autoload
	vendor/bin/phinx migrate
rollback:
	vendor/bin/phinx rollback
seed:
	vendor/bin/phinx seed:run
stop:
	docker-compose stop
down:
	docker-compose down --volumes
key:
	php generate_secret_key.php