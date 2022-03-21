build:
	cp .env.example .env
	php generate_secret_key.php
	docker-compose build
up:
	composer install
	docker-compose up -d
	docker-compose ps
migrate:
	docker-compose stop
	docker-compose up -d
	docker-compose ps
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
generate_keys:
	sudo rm -rf keys/private.key || true
	sudo rm -rf keys/public.key || true
	openssl genrsa -out keys/private.key 2048
	openssl rsa -in keys/private.key -pubout -out keys/public.key
	sudo chown www-data:www-data keys/public.key
	sudo chown www-data:www-data keys/private.key
	sudo chmod 660 keys/public.key
	sudo chmod 660 keys/private.key
	php generate_secret_key.php