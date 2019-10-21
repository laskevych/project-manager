up: docker-up

# Full update
init: docker-dowm docker-pull docker-build docker-up

docker-up:
	docker-compose up -d

docker-dowm:
	docker-compose down --remove-orphans

docker-pull:
	docker-compose pull

docker-build:
	docker-compose build

cli:
	docker-compose run --rm manager-php-cli php bin/app.php