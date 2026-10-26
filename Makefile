.PHONY: up down build restart logs sh artisan migrate seed fresh front front-dev tinker

up:            ## Поднять контейнеры
	docker compose up -d

down:          ## Остановить контейнеры
	docker compose down

build:         ## Пересобрать образ приложения
	docker compose build

restart: down up

logs:          ## Логи приложения
	docker compose logs -f laravel.test

sh:            ## Шелл в контейнере приложения
	docker compose exec laravel.test bash

artisan:       ## make artisan cmd="route:list"
	docker compose exec laravel.test php artisan $(cmd)

migrate:
	docker compose exec laravel.test php artisan migrate

seed:
	docker compose exec laravel.test php artisan db:seed

fresh:         ## Пересоздать БД с сидами (удаляет данные!)
	docker compose exec laravel.test php artisan migrate:fresh --seed

tinker:
	docker compose exec laravel.test php artisan tinker

front:         ## Собрать фронтенд (на хосте, node)
	npm run build

front-dev:     ## Vite dev-сервер с HMR (на хосте)
	npm run dev
