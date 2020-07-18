include .settings

default: sam-build sam-invoke

ARTIFACTS_DIR ?= /tmp/artifacts

BASE_SOURCES = $(wildcard bootstrap*) $(wildcard composer.*) config.php container.php
BASE_ARTIFACTS = $(patsubst %,$(ARTIFACTS_DIR)/%,$(BASE_SOURCES))

RUNTIME_SOURCES = Dockerfile php.ini .settings
RUNTIME_ARTIFACTS = $(ARTIFACTS_DIR)/php74

HELLO_SOURCES = $(wildcard app/**)
HELLO_ARTIFACTS = $(patsubst %,$(ARTIFACTS_DIR)/%,$(HELLO_SOURCES))

$(BASE_ARTIFACTS): $(ARTIFACTS_DIR)/%: %
	@mkdir -p $(dir $@)
	cp $< $@

$(ARTIFACTS_DIR)/vendor: $(BASE_ARTIFACTS)
	composer install --working-dir="${ARTIFACTS_DIR}"

$(RUNTIME_ARTIFACTS): $(RUNTIME_SOURCES)
	docker build --build-arg PHP_PACKAGE=$(PHP_PACKAGE) -t phial-project .
	CONTAINER_ID=$(shell docker run --detach --tty phial-project bash) \
		bash -c 'docker cp "$${CONTAINER_ID}:/opt/$(PHP_PACKAGE)" $(ARTIFACTS_DIR); docker rm --force $${CONTAINER_ID}'

$(HELLO_ARTIFACTS): $(ARTIFACTS_DIR)/%: %
	@mkdir -p $(dir $@)
	cp $< $@

build-Runtime: $(RUNTIME_ARTIFACTS)
	@tree $(ARTIFACTS_DIR)

build-Hello: $(ARTIFACTS_DIR)/vendor $(HELLO_ARTIFACTS)

sam-api:
	sam local start-api

sam-build:
	sam build

sam-invoke:
	sam local invoke

sam-deploy:
	sam deploy

clean:
	rm -rf $(ARTIFACTS_DIR)/*

docker-build:
	docker build --build-arg PHP_PACKAGE=$(PHP_PACKAGE) -t phial-project .

phpstan:
	phpstan analyse --level 8 app/ bootstrap.php config.php container.php

rector:
	docker run --rm \
		-v $(shell pwd):/project \
		rector/rector:latest process /project/app \
		--config /project/rector.yaml \
		--autoload-file /project/vendor/autoload.php

run:
	docker run -it --rm phial-project bash
