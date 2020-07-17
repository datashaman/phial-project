include .settings

sam-api:
	sam local start-api

sam-build:
	sam build

sam-invoke:
	sam local invoke

sam-deploy:
	sam deploy

build-Runtime:
	PHP_PACKAGE=$(PHP_PACKAGE) ./build-Runtime.sh

build-HelloWorldFunction:
	PHP_PACKAGE=$(PHP_PACKAGE) ./build-HelloWorldFunction.sh

docker-build:
	docker build --build-arg PHP_PACKAGE=$(PHP_PACKAGE) -t phial-project .

rector:
	docker run --rm \
		-v $(shell pwd):/project \
		rector/rector:latest process /project/app \
		--config /project/rector.yaml \
		--autoload-file /project/vendor/autoload.php

run:
	docker run -it --rm phial-project bash
