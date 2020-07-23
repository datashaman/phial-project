include .settings

default: docker-Runtime sam-build # local-invoke-hello local-invoke-queue

ARTIFACTS_DIR ?= /tmp/artifacts

BASE_SOURCES = bootstrap.php config.php $(wildcard bootstrap/*) $(wildcard composer.*)
BASE_ARTIFACTS = $(patsubst %,$(ARTIFACTS_DIR)/%,$(BASE_SOURCES))

RUNTIME_SOURCES = .dockerignore Dockerfile php.ini .settings
RUNTIME_ARTIFACTS = $(ARTIFACTS_DIR)/${PHP_PACKAGE}

HELLO_SOURCES = $(wildcard app/Abstract*) app/HelloHandler.php
HELLO_ARTIFACTS = $(patsubst %,$(ARTIFACTS_DIR)/%,$(HELLO_SOURCES))

QUEUE_SOURCES = $(wildcard app/Abstract*) app/QueueHandler.php
QUEUE_ARTIFACTS = $(patsubst %,$(ARTIFACTS_DIR)/%,$(QUEUE_SOURCES))

$(BASE_ARTIFACTS): $(ARTIFACTS_DIR)/%: %
	@mkdir -p $(dir $@)
	cp $< $@

$(ARTIFACTS_DIR)/vendor: $(BASE_ARTIFACTS)
	composer install --working-dir="${ARTIFACTS_DIR}"

$(RUNTIME_ARTIFACTS): $(RUNTIME_SOURCES)
	CONTAINER_ID=$(shell docker run --detach --tty phial-project bash) \
		bash -c 'docker cp "$${CONTAINER_ID}:/opt/$(PHP_PACKAGE)" $(ARTIFACTS_DIR); docker cp "$${CONTAINER_ID}:/opt/bootstrap" $(ARTIFACTS_DIR); docker rm --force $${CONTAINER_ID}'

$(SHARED_ARTIFACTS): $(SHARED_SOURCES)
	@mkdir -p $(dir $@)
	cp $< $@

$(HELLO_ARTIFACTS): $(ARTIFACTS_DIR)/%: %
	@mkdir -p $(dir $@)
	cp $< $@

$(QUEUE_ARTIFACTS): $(ARTIFACTS_DIR)/%: %
	@mkdir -p $(dir $@)
	cp $< $@

build-Runtime: $(RUNTIME_ARTIFACTS)
build-HelloHandler: $(ARTIFACTS_DIR)/vendor $(HELLO_ARTIFACTS)
build-QueueHandler: $(ARTIFACTS_DIR)/vendor $(QUEUE_ARTIFACTS)

clean:
	rm -rf $(ARTIFACTS_DIR)/*

docker-Runtime:
	docker build --build-arg PHP_PACKAGE=$(PHP_PACKAGE) --pull -t phial-project .

local-api:
	sam local start-api

local-invoke-hello:
	sam local generate-event apigateway aws-proxy --path hello > events/hello.json
	sam local invoke --event events/hello.json HelloHandler

local-invoke-queue:
	sam local generate-event sqs receive-message --queue-name Queue > events/queue.json
	sam local invoke --event events/queue.json QueueHandler

phpstan:
	phpstan analyse --level 8 app/ bootstrap/

rector:
	docker run --rm \
		-v $(shell pwd):/project \
		rector/rector:latest process /project/app \
		--config /project/rector.yaml \
		--autoload-file /project/vendor/autoload.php

require-handler:
	composer clear-cache
	composer require datashaman/phial-handler:dev-master

run:
	docker run -it --rm phial-project bash

sam-build:
	sam build

sam-deploy: sam-build
	sam deploy
