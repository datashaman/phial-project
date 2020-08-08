include .settings

ARTIFACTS_DIR ?= .aws-sam/build
INVOKE_PATH ?=
PROJECT = phial-project
SOURCES = app bootstrap.php config routes

default: build

build:
	sam build

rebuild: clean build

clean:
	rm -rf cache/* $(ARTIFACTS_DIR)

docker-run:
	docker run -it --rm \
		-v $(shell pwd):/var/task \
		-v $(shell pwd)/php.ini:/opt/$(PHP_PACKAGE)/etc/php.d/$(PROJECT).ini \
		-w /var/task \
		$(PROJECT):$(PHP_PACKAGE)

docker-Runtime:
	docker build --build-arg PHP_PACKAGE=$(PHP_PACKAGE) --pull -t $(PROJECT):$(PHP_PACKAGE) .
	CONTAINER_ID=$(shell docker run --detach --tty $(PROJECT):$(PHP_PACKAGE) bash) \
		bash -c 'docker cp "$${CONTAINER_ID}:/opt/$(PHP_PACKAGE)" "$(ARTIFACTS_DIR)"; docker cp "$${CONTAINER_ID}:/opt/bootstrap" "$(ARTIFACTS_DIR)"; docker rm --force $${CONTAINER_ID}'

build-Runtime: docker-Runtime

build-App:
	mkdir -p "$(ARTIFACTS_DIR)"
	cp -a app bootstrap.php composer.json composer.lock config routes "$(ARTIFACTS_DIR)"
	composer install --no-dev --working-dir "$(ARTIFACTS_DIR)"

local-api:
	sam local start-api

local-invoke: build
	sam local generate-event apigateway aws-proxy --method GET --path "$(INVOKE_PATH)" > events/request.json
	sam local invoke --event events/request.json App

logs:
	sam logs -n App --stack-name $(PROJECT) --tail

code-quality: code-phpstan code-rector code-phpcs

code-phpcbf:
	phpcbf $(SOURCES)

code-phpcs:
	phpcs $(SOURCES)

code-phpstan:
	phpstan analyse

code-rector:
	rector process $(SOURCES)
