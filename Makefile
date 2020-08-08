include .settings

ARTIFACTS_DIR ?= .aws-sam/build
INVOKE_PATH ?=
SOURCES = app bootstrap.php config routes

default: build local-invoke

build:
	sam build

rebuild: clean build

clean:
	rm -rf cache/* $(ARTIFACTS_DIR)

docker-Runtime:
	docker build --build-arg PHP_PACKAGE=$(PHP_PACKAGE) --pull -t phial-project .
	CONTAINER_ID=$(shell docker run --detach --tty phial-project bash) \
		bash -c 'docker cp "$${CONTAINER_ID}:/opt/$(PHP_PACKAGE)" "$(ARTIFACTS_DIR)"; docker cp "$${CONTAINER_ID}:/opt/bootstrap" "$(ARTIFACTS_DIR)"; docker rm --force $${CONTAINER_ID}'

build-Runtime: docker-Runtime

build-App:
	mkdir -p "$(ARTIFACTS_DIR)"
	cp -a app bootstrap.php cache composer.json composer.lock config routes "$(ARTIFACTS_DIR)"
	composer install --optimize-autoloader --no-dev --working-dir="$(ARTIFACTS_DIR)"
	php cache.php "$(ARTIFACTS_DIR)"

local-api:
	sam local start-api --debug

local-invoke:
	sam local generate-event apigateway aws-proxy --method GET --path "$(INVOKE_PATH)" > events/request.json
	sam local invoke --event events/request.json App

code-quality: code-phpstan code-rector code-phpcs

code-phpcbf:
	phpcbf $(SOURCES)

code-phpcs:
	phpcs $(SOURCES)

code-phpstan:
	phpstan analyse --level max $(SOURCES)

code-rector:
	rector process $(SOURCES)
