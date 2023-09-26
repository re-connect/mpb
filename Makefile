BIN     	  = ./vendor/bin
RECTOR        = $(BIN)/rector
PHPSTAN       = $(BIN)/phpstan
PHPMD         = $(BIN)/phpmd
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

md:
	@$(PHPMD) src text phpmd-ruleset.xml

fixer:
	@$(PHP_CS_FIXER) fix src --allow-risky=yes
	@$(PHP_CS_FIXER) fix tests --allow-risky=yes

dep: deploy

deploy:
	@$(DEPLOYER) deploy