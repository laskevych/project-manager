up: docker-up

# Full update
init: docker-dowm docker-pull docker-build docker-up manager-init
test: manager-test

docker-up:
	docker-compose up -d

docker-dowm:
	docker-compose down --remove-orphans

docker-pull:
	docker-compose pull

docker-build:
	docker-compose build

manager-init: manager-composer-install manager-wait-db manager-migrations

manager-composer-install:
	docker-compose run --rm manager-php-cli composer install

manager-wait-db:
	until docker-compose exec -T manager-postgres pg_isready --timeout=0 --dbname=app ; do sleep 1 ; done

manager-migrations:
	docker-compose run --rm manager-php-cli php bin/console doctrine:migrations:migrate --no-interaction

manager-test:
	docker-compose run --rm manager-php-cli php bin/phpunit

cli:
	docker-compose run --rm manager-php-cli php bin/app.php