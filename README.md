# SLIM тестовое задание

## Требования
- [Docker](https://www.docker.com)
- [Docker Compose](https://docs.docker.com/compose/)
- [Composer](https://getcomposer.org)
- [Git](https://git-scm.com)
- Linux (желательно, т.к. необходимо генерировать ключи)

## Порядок установки:

### 1. Развернуть проект.
Склонировать проект локально.
```
git clone https://github.com/bibilka/slim-test

cd slim-test
```
Собрать проект (будет выполнен build для контейнеров докера, создан файл с конфигурационными параметрами `.env`): 
```
make build
```
### 2. Генерация секретных ключей.
Сгенерировать пару public/private ключей. Эти ключи используются для подписи и проверки передаваемых JWT.
```
make generate_keys
```
**(Опциально)** Если при выполнении этой команды возникнут проблемы или ошибки, тогда ключи необходимо сгенерировать вручную:
```
sudo rm -rf keys/private.key || true
sudo rm -rf keys/public.key || true

openssl genrsa -out keys/private.key 2048
openssl rsa -in keys/private.key -pubout -out keys/public.key
sudo chown www-data:www-data keys/public.key
sudo chown www-data:www-data keys/private.key
sudo chmod 660 keys/public.key
sudo chmod 660 keys/private.key

php generate_secret_key.php
```
### 3. Запуск и настройка.
А затем запустить (будет выполнен up для контейнеров докера и composer install): 
```
make up
```
Применить миграции базы данных:
```
make migrate
```
Дополнительные возможности:

- Генерация SECRET_KEY осуществляется с помощью команды `make key`
- Конфигурационные параметры можно отредактировать в файле `.env`
- Остановить проект: `make stop`
- Выполнить rollback миграции базы данных: `make rollback`
- Выполнить seed базы данных: `make seed`

### 4. Готово

Работающая версия проекта доступна по адресу: `http://localhost/`
- Регистрация: `http://localhost/register`
- Вход (авторизация): `http://localhost/login`
- Домашняя страница: `http://localhost/app/home`
- Страница профиля: `http://localhost/app/profile`
- Swagger (API документация): `http://localhost/docs/api/v1`