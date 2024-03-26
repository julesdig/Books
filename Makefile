# Variables
DOCKER = docker
ENTER = $(DOCKER) exec -it books_php

## 💻 Coding standards
analyse: ## Run all analyse
	$(MAKE) phpstan
	$(MAKE) phpcbf
	$(MAKE) phpcs
	$(MAKE) phpmd
	
phpcs: ## Run PHP CS Sniffer
	$(ENTER) bin/phpcs src/
phpcbf: ## Run PHP CS Fixer
	$(ENTER)  bin/phpcbf  src/
phpstan: ## Run PHPStan
	$(ENTER) bin/phpstan analyse src/
phpmd: ## Run PHP Mess Detector
	$(ENTER) bin/phpmd src/ text phpmd.xml

## —— 🛠️  Others ——
help: ## List of commands
	@grep -E '(^[a-zA-Z0-9_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'



