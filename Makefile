install:
	docker-compose up -d --build
	docker-compose exec backend composer install
	docker-compose exec backend bin/console doctrine:migration:migrate
	docker-compose exec backend bash install.sh