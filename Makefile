up:
	docker compose up -d

down:
	docker compose down -v

build:
	docker compose up -d --build

bash:
	docker compose exec php bash

go:
	docker compose exec php bash

migration:
	docker compose exec php php bin/console make:migration --no-interaction
	docker compose exec php php bin/console doctrine:migrations:migrate --no-interaction

migrate:
	docker compose exec php php bin/console doctrine:migrations:migrate --no-interaction

test:
	docker compose exec php php bin/phpunit #--debug

schema-update:
	docker compose exec php php bin/console doctrine:schema:update --force

cache:
	docker compose exec php php bin/console cache:clear

fixtures:
	docker compose exec php php bin/console doctrine:fixtures:load --no-interaction

jwt:
	docker compose exec php php bin/console lexik:jwt:generate-keypair

composer:
	docker compose exec php composer install

setup: composer migrate fixtures jwt cache


route:
	docker compose exec php php bin/console debug:route


