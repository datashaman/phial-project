DIR := $(shell dirname $(realpath $(firstword $(MAKEFILE_LIST))))

build-HelloWorldFunction:
	$(DIR)/build.sh

rector:
	docker run --rm \
		-v $(shell pwd):/project \
		rector/rector:latest process /project/app \
		--config /project/rector.yaml \
		--autoload-file /project/vendor/autoload.php
