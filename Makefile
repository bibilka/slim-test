build:
	cp .env.example .env
	php generate_secret_key.php
	docker-compose build
up:
	composer install
	docker-compose up -d
	docker-compose ps
	sleep 1
	vendor/bin/phinx migrate
migrate:
	vendor/bin/phinx migrate
seed:
	vendor/bin/phinx seed:run
stop:
	docker-compose stop
down:
	docker-compose down --volumes
key:
	php generate_secret_key.php