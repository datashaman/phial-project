include .settings

default: sam-build sam-invoke-queue sam-invoke-hello

ARTIFACTS_DIR ?= /tmp/artifacts

BASE_SOURCES = $(wildcard bootstrap/*) $(wildcard composer.*)
BASE_ARTIFACTS = $(patsubst %,$(ARTIFACTS_DIR)/%,$(BASE_SOURCES))

RUNTIME_SOURCES = .dockerignore Dockerfile php.ini .settings
RUNTIME_ARTIFACTS = $(ARTIFACTS_DIR)/php74

SHARED_SOURCES = app/AbstractHandler.php
SHARED_ARTIFACTS = $(patsubst %,$(ARTIFACTS_DIR)/%,$(SHARED_SOURCES))

HELLO_SOURCES = app/HelloHandler.php
HELLO_ARTIFACTS = $(patsubst %,$(ARTIFACTS_DIR)/%,$(HELLO_SOURCES))

QUEUE_SOURCES = app/QueueHandler.php
QUEUE_ARTIFACTS = $(patsubst %,$(ARTIFACTS_DIR)/%,$(QUEUE_SOURCES))

$(BASE_ARTIFACTS): $(ARTIFACTS_DIR)/%: %
	@mkdir -p $(dir $@)
	cp $< $@

$(ARTIFACTS_DIR)/vendor: $(BASE_ARTIFACTS)
	composer install --working-dir="${ARTIFACTS_DIR}"

$(RUNTIME_ARTIFACTS): $(RUNTIME_SOURCES)
	docker build --build-arg PHP_PACKAGE=$(PHP_PACKAGE) --pull -t phial-project .
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
build-HelloHandler: $(ARTIFACTS_DIR)/vendor $(SHARED_ARTIFACTS) $(HELLO_ARTIFACTS)
build-QueueHandler: $(ARTIFACTS_DIR)/vendor $(SHARED_ARTIFACTS) $(QUEUE_ARTIFACTS)

sam-api:
	sam local start-api

sam-build:
	sam build

sam-deploy:
	sam deploy

sam-invoke-hello:
	sam local invoke HelloHandler

sam-invoke-queue:
	sam local invoke QueueHandler

sam-deploy:
	sam deploy

clean:
	rm -rf $(ARTIFACTS_DIR)/*

docker-build:
	docker build --build-arg PHP_PACKAGE=$(PHP_PACKAGE) --pull -t phial-project .

phpstan:
	phpstan analyse --level 8 app/ bootstrap/

rector:
	docker run --rm \
		-v $(shell pwd):/project \
		rector/rector:latest process /project/app \
		--config /project/rector.yaml \
		--autoload-file /project/vendor/autoload.php

run:
	docker run -it --rm phial-project bash
