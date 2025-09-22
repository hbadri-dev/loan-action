.PHONY: help build up down restart logs shell composer npm test pint

help: ## نمایش راهنما
	@echo "دستورات موجود:"
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}'

build: ## ساخت Docker images
	docker-compose build

up: ## راه‌اندازی containers
	docker-compose up -d

down: ## متوقف کردن containers
	docker-compose down

restart: ## راه‌اندازی مجدد containers
	docker-compose restart

logs: ## نمایش logs
	docker-compose logs -f

migrate: ## اجرای migrations با seed
	docker exec loan-auction-app php artisan migrate --seed

npm: ## اجرای npm run dev
	docker exec -it loan-auction-app npm run dev

shell: ## دسترسی به shell داخل container
	docker-compose exec app bash

composer: ## اجرای composer commands
	docker-compose exec app composer $(filter-out $@,$(MAKECMDGOALS))

artisan: ## اجرای artisan commands
	docker-compose exec app php artisan $(filter-out $@,$(MAKECMDGOALS))

test: ## اجرای tests
	docker-compose exec app php artisan test

pint: ## اجرای Laravel Pint
	docker-compose exec app ./vendor/bin/pint

seed: ## اجرای seeders
	docker-compose exec app php artisan db:seed

fresh: ## اجرای migrate:fresh با seed
	docker-compose exec app php artisan migrate:fresh --seed

clear: ## پاک کردن cache ها
	docker-compose exec app php artisan cache:clear
	docker-compose exec app php artisan config:clear
	docker-compose exec app php artisan route:clear
	docker-compose exec app php artisan view:clear

install: ## نصب کامل پروژه
	@echo "راه‌اندازی پروژه..."
	docker-compose up -d
	@echo "منتظر راه‌اندازی MySQL..."
	sleep 30
	docker-compose exec app composer install
	docker-compose exec app npm install
	docker-compose exec app php artisan key:generate
	docker-compose exec app php artisan migrate --force
	docker-compose exec app php artisan db:seed --force
	docker-compose exec app npm run build
	@echo "پروژه با موفقیت راه‌اندازی شد!"
	@echo "دسترسی: http://localhost:8080"

%:
	@:
