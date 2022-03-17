build:
	cp .env.example .env
	docker-compose build
up:
	composer install
	docker-compose up -d
	docker-compose ps
stop:
	docker-compose stop
down:
	docker-compose down --volumes