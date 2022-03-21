# SLIM тестовое задание

## Требования
- [Docker](https://www.docker.com)
- [Docker Compose](https://docs.docker.com/compose/)
- [Composer](https://getcomposer.org)
- [Git](https://git-scm.com)
- Linux (желательно, т.к. необходимо генерировать ключи)

Порядок установки:
## 1. Склонировать проект локально.
```
git clone https://github.com/bibilka/slim-test

cd slim-test
```
## 2. Развернуть проект.

Собрать проект (будет выполнен build для контейнеров докера, создан файл с конфигурационными параметрами `.env` и сгенерирован случайный секретный ключ): 
```
make build
```
Сгенерировать пару public/private ключей. Эти ключи используются для подписи и проверки передаваемых JWT.
```
openssl genrsa -out keys/private.key 2048
openssl rsa -in keys/private.key -pubout -out keys/public.key
sudo chown www-data:www-data keys/public.key
sudo chown www-data:www-data keys/private.key
sudo chmod 660 keys/public.key
sudo chmod 660 keys/private.key
```
А затем запустить (будет выполнен up для контейнеров докера, будут выполнены миграции и composer install): 
```
make up
```
- Генерация SECRET_KEY осуществляется с помощью команды `make key`
- Конфигурационные параметры можно отредактировать в файле `.env`
- Остановить проект: `make stop`
- Выполнить миграции базы данных: `make migrate`
- Выполнить seed базы данных: `make seed`

## 3. Готово

Работающая версия проекта доступна по адресу: `http://localhost/`
- Регистрация: `http://localhost/register`
- Вход (авторизация): `http://localhost/login`
- Домашняя страница: `http://localhost/app/home`
- Страница профиля: `http://localhost/app/profile`
- Swagger (API документация): `http://localhost/docs/api/v1`