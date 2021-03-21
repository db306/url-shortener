.DEFAULT_GOAL := help

.PHONY: help fix reload test stan stan-cache

fix: ## Fixes php Lint
	docker run --rm -it -w=/app -v ${PWD}:/app oskarstark/php-cs-fixer-ga:2.17.0

start: ## Starts a new development environment
	docker-compose up -d

reload: ## Reloads all test data
	php bin/console doctrine:database:create --env=test --if-not-exists
	php bin/console doctrine:migrations:migrate --env=test --no-interaction
	php bin/console doctrine:fixtures:load --env=test --no-interaction

stan:
	./vendor/bin/phpstan analyse src tests --level 2 --memory-limit 1G

stan-cache:
	./vendor/bin/phpstan clear-result-cache

help: ## Show this help
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "\033[036m%-30s\033[0m %s\n", $$1, $$2}' ${MAKEFILE_LIST}

test: ## Run tests locally
	vendor/phpunit/phpunit/phpunit --configuration phpunit.xml