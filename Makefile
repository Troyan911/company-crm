up:
	docker compose up -d

stop:
	docker compose down

down:
	docker compose down

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
	docker compose exec php php bin/phpunit tests/Unit  #--debug
	docker compose exec php php bin/phpunit tests/Api  #--debug

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

test-setup:
	docker compose exec php php bin/console doctrine:database:create --env=test  --no-interaction
	docker compose exec php php bin/console doctrine:migrations:migrate --env=test  --no-interaction
	docker compose exec php php bin/console doctrine:fixtures:load --env=test  --no-interaction


