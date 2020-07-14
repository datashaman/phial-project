DIR := $(shell dirname $(realpath $(firstword $(MAKEFILE_LIST))))

build-HelloWorldFunction:
	$(DIR)/build.sh
