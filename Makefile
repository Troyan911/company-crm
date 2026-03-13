up:
	docker compose up -d

down:
	docker compose down

build:
	docker compose up -d --build

bash:
	docker compose exec php bash

console:
	docker compose exec php php bin/console

db-create:
	docker compose exec php php bin/console doctrine:database:create --if-not-exists

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

setup:
	docker compose exec php php bin/console doctrine:database:create --if-not-exists
	docker compose exec php php bin/console doctrine:migrations:migrate --no-interaction
	docker compose exec php php bin/console cache:clear


