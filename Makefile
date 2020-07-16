build:
	sam build

invoke:
	sam local invoke

deploy:
	sam deploy

build-Runtime:
	./build-runtime.sh

build-HelloWorldFunction:
	./build-function.sh

rector:
	docker run --rm \
		-v $(shell pwd):/project \
		rector/rector:latest process /project/app \
		--config /project/rector.yaml \
		--autoload-file /project/vendor/autoload.php
