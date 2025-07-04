.PHONY: it
it: tools vendor cs phpstan tests

.PHONY: tools
tools: phive

.PHONY: cs
cs: php-cs-fixer-fix ## Lints, normalizes, and fixes code style issues

.PHONY: php-cs-fixer-fix
php-cs-fixer-fix: phive vendor
	php-cs-fixer fix

.PHONY: phive
phive: ## Installs tools via PHIVE
	PHIVE_HOME=.build/phive phive install

.PHONY: phive phpstan
phpstan: vendor ## Runs phpstan against fixtures and library
	phpstan analyse --memory-limit 1G -l9 src -v

.PHONY: dependency-analysis
dependency-analysis: phive vendor ## Runs a dependency analysis with maglnet/composer-require-checker
	composer-require-checker check --config-file=$(shell pwd)/composer-require-checker.json --verbose

.PHONY: help
help: ## Displays this list of targets with descriptions
	@grep -E '^[a-zA-Z0-9_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}'

.PHONY: tests
tests: phpunit ## Tests code

.PHONY: phpunit
phpunit: vendor
	./vendor/bin/phpunit

.PHONY: clover
clover: vendor
	XDEBUG_MODE=coverage ./vendor/bin/phpunit --coverage-clover=coverage.clover

vendor: composer.json
	composer validate --strict
	composer install --no-interaction --no-progress

.PHONY: clean
clean: 
	rm -rf vendor tools

.PHONY: realclean
realclean: clean
	rm -rf composer.lock
