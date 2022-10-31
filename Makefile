BIN     	  = ./vendor/bin
RECTOR        = $(BIN)/rector
PHPSTAN       = $(BIN)/phpstan
PHP_CS_FIXER  = $(BIN)/php-cs-fixer
PHPUNIT		  = $(BIN)/simple-phpunit
DEPLOYER      = $(BIN)/dep

.PHONY        :

cs: rector stan fixer test

test:
	@$(PHPUNIT) tests

rector:
	@$(RECTOR) process --clear-cache

stan:
	@$(PHPSTAN) analyse

fixer:
	@$(PHP_CS_FIXER) fix src --allow-risky=yes
	@$(PHP_CS_FIXER) fix tests --allow-risky=yes

dep: deploy

deploy:
	@$(DEPLOYER) deploy