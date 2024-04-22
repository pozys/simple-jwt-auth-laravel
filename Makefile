PORT ?= 8080

console:
	php artisan tinker

test:
	php artisan test

start:
	php artisan serve --host 0.0.0.0 --port ${PORT}

setup: env-prepare sqlite-prepare install key jwt-secret db-prepare

install:
	composer install

env-prepare:
	cp -n .env.example .env || true

sqlite-prepare:
	touch database/database.sqlite

db-prepare:
	php artisan migrate:fresh --force --seed

key:
	php artisan key:generate

jwt-secret:
	php artisan jwt:secret
