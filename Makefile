include .settings

default: docker-Runtime sam-build local-invoke-handler

ARTIFACTS_DIR ?= /tmp/artifacts

BASE_SOURCES = composer.json composer.lock
BASE_ARTIFACTS = $(patsubst %,$(ARTIFACTS_DIR)/%,$(BASE_SOURCES))

RUNTIME_SOURCES = .dockerignore Dockerfile php.ini .settings
RUNTIME_ARTIFACTS = $(ARTIFACTS_DIR)/${PHP_PACKAGE}

APP_SOURCES = bootstrap.php $(shell find app config -type f)
APP_ARTIFACTS = $(patsubst %,$(ARTIFACTS_DIR)/%,$(APP_SOURCES))

$(BASE_ARTIFACTS): $(ARTIFACTS_DIR)/%: %
	@mkdir -p $(dir $@)
	cp $< $@

$(ARTIFACTS_DIR)/vendor: $(BASE_ARTIFACTS)
	composer install --optimize-autoloader --working-dir="${ARTIFACTS_DIR}"

$(RUNTIME_ARTIFACTS): $(RUNTIME_SOURCES)
	CONTAINER_ID=$(shell docker run --detach --tty phial-project bash) \
		bash -c 'docker cp "$${CONTAINER_ID}:/opt/$(PHP_PACKAGE)" $(ARTIFACTS_DIR); docker cp "$${CONTAINER_ID}:/opt/bootstrap" $(ARTIFACTS_DIR); docker rm --force $${CONTAINER_ID}'

$(SHARED_ARTIFACTS): $(SHARED_SOURCES)
	@mkdir -p $(dir $@)
	cp $< $@

$(APP_ARTIFACTS): $(ARTIFACTS_DIR)/%: %
	@mkdir -p $(dir $@)
	cp $< $@

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

code-quality: code-phpstan code-rector

code-phpstan:
	phpstan analyse --level max app/ bootstrap/

code-rector:
	rector process app/ bootstrap/

require-handler:
	composer clear-cache
	composer require datashaman/phial-handler:dev-master

run:
	docker run -it --rm phial-project bash

sam-build:
	sam build

sam-deploy: sam-build
	sam deploy
