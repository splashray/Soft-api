up:
	docker compose up -d --build

down:
	docker compose down -v

install:
	docker compose run --rm composer install

migrate:
	docker compose exec app php artisan migrate

seed:
	docker compose exec app php artisan db:seed

key:
	docker compose exec app php artisan key:generate

test:
	docker compose exec app php artisan test


