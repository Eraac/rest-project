test:
	php bin/console doctrine:database:drop --force --if-exists
	php bin/console doctrine:database:create
	php bin/console doctrine:schema:update --force
	php bin/console doctrine:fixtures:load --no-interaction
	phpunit
