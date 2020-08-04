include .settings

ARTIFACTS_DIR ?= /tmp/artifacts
STACK_NAME ?= phial-project

BASE_SOURCES = composer.json composer.lock
BASE_ARTIFACTS = $(patsubst %,$(ARTIFACTS_DIR)/%,$(BASE_SOURCES))

RUNTIME_SOURCES = .dockerignore Dockerfile php.ini .settings
RUNTIME_ARTIFACTS = $(ARTIFACTS_DIR)/${PHP_PACKAGE}

APP_SOURCES = bootstrap.php cache $(shell find app config -type f)
APP_ARTIFACTS = $(patsubst %,$(ARTIFACTS_DIR)/%,$(APP_SOURCES))

$(BASE_ARTIFACTS): $(ARTIFACTS_DIR)/%: %
	@mkdir -p $(dir $@)
	cp -a $< $@

$(ARTIFACTS_DIR)/vendor: $(BASE_ARTIFACTS)
	composer install --optimize-autoloader --working-dir="${ARTIFACTS_DIR}"

$(RUNTIME_ARTIFACTS): $(RUNTIME_SOURCES)
	CONTAINER_ID=$(shell docker run --detach --tty phial-project bash) \
		bash -c 'docker cp "$${CONTAINER_ID}:/opt/$(PHP_PACKAGE)" $(ARTIFACTS_DIR); docker cp "$${CONTAINER_ID}:/opt/bootstrap" $(ARTIFACTS_DIR); docker rm --force $${CONTAINER_ID}'

$(SHARED_ARTIFACTS): $(SHARED_SOURCES)
	@mkdir -p $(dir $@)
	cp -a $< $@

$(APP_ARTIFACTS): $(ARTIFACTS_DIR)/%: %
	@mkdir -p $(dir $@)
	cp -a $< $@
	php build.php

build-Runtime: $(RUNTIME_ARTIFACTS)
build-RequestHandler: $(ARTIFACTS_DIR)/vendor $(APP_ARTIFACTS)

clean:
	rm -rf $(ARTIFACTS_DIR)/*

docker-Runtime:
	docker build --build-arg PHP_PACKAGE=$(PHP_PACKAGE) --pull -t phial-project .

local-api:
	sam local start-api

local-invoke-handler:
	sam local generate-event apigateway aws-proxy --method GET --path '' > events/request.json
	sam local invoke --event events/request.json RequestHandler

code-quality: code-phpcs code-phpstan code-rector

code-phpcbf:
	phpcbf $(APP_SOURCES)

code-phpcs:
	phpcs $(APP_SOURCES)

code-phpstan:
	phpstan analyse --level max $(APP_SOURCES)

code-rector:
	rector process $(APP_SOURCES)

require-handler:
	composer clear-cache
	composer require datashaman/phial-handler:dev-master

run:
	docker run -it --rm phial-project bash

sam-build:
	sam build

sam-deploy: sam-build
	sam deploy

sam-logs:
	sam logs --name RequestHandler --stack-name $(STACK_NAME)
