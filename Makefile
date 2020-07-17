include .settings

sam-build:
	sam build

sam-invoke:
	sam local invoke

sam-deploy:
	sam deploy

build-Runtime:
	PHP_PACKAGE=$(PHP_PACKAGE) ./build-runtime.sh

build-HelloWorldFunction:
	./build-function.sh

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
