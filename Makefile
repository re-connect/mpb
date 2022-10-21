BIN     	  = ./vendor/bin
RECTOR        = $(BIN)/rector
PHPSTAN       = $(BIN)/phpstan
PHP_CS_FIXER  = $(BIN)/php-cs-fixer
PHPUNIT		  = $(BIN)/simple-phpunit
DEPLOYER	  = $(BIN)/dep

.PHONY        :

cs: rector stan lint

test:
	@$(PHPUNIT)

rector:
	@$(RECTOR) process

stan:
	@$(PHPSTAN) analyse

deploy:
	@$(DEPLOYER) deploy

lint:
	@$(PHP_CS_FIXER) fix src --allow-risky=yes
	@$(PHP_CS_FIXER) fix tests --allow-risky=yes
