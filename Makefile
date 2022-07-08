BIN     	  = ./vendor/bin
RECTOR        = $(BIN)/rector
PHPSTAN       = $(BIN)/phpstan
PHP_CS_FIXER  = $(BIN)/php-cs-fixer

.PHONY        : # Not needed here, but you can put your all your targets to be sure

cs: rector stan lint ## Run all coding standards checks

rector: ## Run Rector
	@$(RECTOR) process

stan: ## Run PHPStan
	@$(PHPSTAN) analyse

lint: ## Fix files with php-cs-fixer
	@$(PHP_CS_FIXER) fix src --allow-risky=yes
	@$(PHP_CS_FIXER) fix tests --allow-risky=yes
