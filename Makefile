export

ifndef BUILD_TAG
	BUILD_TAG:=$(shell date +'%Y-%m-%d-%H-%M-%S')-$(shell git rev-parse --short HEAD)
endif

echo-build-tag:
	@echo $(BUILD_TAG)

.DEFAULT_GOAL := help

.PHONY: help fix reload test stan stan-cache

fix: ## Fixes php Lint
	docker run --rm -it -w=/app -v ${PWD}:/app oskarstark/php-cs-fixer-ga:2.17.0

help: ## Show this help
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "\033[036m%-30s\033[0m %s\n", $$1, $$2}' ${MAKEFILE_LIST}

code-style:
	./vendor/squizlabs/php_codesniffer/bin/phpcs --standard=phpcs.xml.dist -n -p src/

start:
	symfony serve

watch:
	yarn run encore dev --watch

reload:
	php bin/console test:drop --env=test
	php bin/console doctrine:database:create --env=test --if-not-exists
	php bin/console doctrine:migrations:migrate --env=test --no-interaction
	php bin/console doctrine:fixtures:load --append --env=test

test:
	./vendor/bin/simple-phpunit --no-coverage --configuration phpunit.xml.dist

stan:
	./vendor/bin/phpstan analyse src tests --level 2 --memory-limit 1G

stan-cache:
	./vendor/bin/phpstan clear-result-cache
