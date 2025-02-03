.PHONY: it
it: tools vendor

.PHONY: tools
tools: phive

.PHONY: cs
cs: yamllint php-cs-fixer ## Lints, normalizes, and fixes code style issues
	composer normalize

.PHONY: yamllint
yamllint: phive
	yamllint -c .yamllint.yaml --strict .

.PHONY: php-cs-fixer
php-cs-fixer: phive vendor
	php-cs-fixer fix

.PHONY: phive
phive: ## Installs tools via PHIVE
	PHIVE_HOME=.build/phive phive install

phpstan: ## Runs phpstan against example and library
	phpstan analyse --memory-limit 1G -l9 src

.PHONY: help
help: ## Displays this list of targets with descriptions
	@grep -E '^[a-zA-Z0-9_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}'

test: phive vendor ## Tests code
	phpunit

vendor: composer.json
	composer validate --strict
	composer install --no-interaction --no-progress

.PHONY: clean
clean: 
	rm -rf vendor tools

.PHONY: realclean
realclean: clean
	rm -rf composer.lock
