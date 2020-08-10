include .settings
include .env

ARTIFACTS_DIR ?= .aws-sam/build
INVOKE_PATH ?=
PROJECT = phial-project
PROJECT_SID = phialproject
SOURCES = app bootstrap.php config routes

RDS_SECURITY_GROUP_IDS = $(shell aws ec2 describe-security-groups --filter "Name=vpc-id,Values=$(VPC_ID)" --query "SecurityGroups[].GroupId | join(',', @)" --output text)
RDS_SUBNET_IDS = $(shell aws rds describe-db-subnet-groups --filter "Name=DBSubnetGroupName,Values=$(RDS_SUBNET_GROUP_NAME)" --query "DBSubnetGroups[].Subnets[].SubnetIdentifier | join(',', @)" --output text)
VPC_ID = $(shell aws ec2 describe-vpcs --query "Vpcs[0].VpcId" --output text)
VPC_SUBNET_IDS = $(shell aws ec2 describe-subnets --filter Name=vpc-id,Values=$(VPC_ID) --query 'Subnets[].SubnetId' --output text)

default: sam-build

sam-build:
	sam build

sam-deploy: sam-build
	sam deploy

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
	composer install --working-dir "$(ARTIFACTS_DIR)"

local-api:
	sam local start-api --parameter-overrides $(PARAMETER_OVERRIDES)

local-invoke: sam-build
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
	rector process

rds-create-db-subnet-group:
	aws rds create-db-subnet-group \
		--db-subnet-group-description 'Default RDS' \
		--db-subnet-group-name $(RDS_SUBNET_GROUP_NAME) \
		--subnet-ids $(VPC_SUBNET_IDS)

rds-create-db-cluster:
	aws rds create-db-cluster \
		--db-cluster-identifier $(PROJECT) \
		--db-subnet-group-name $(RDS_SUBNET_GROUP_NAME) \
		--engine aurora-mysql \
		--engine-mode serverless \
		--engine-version 5.7.12 \
		--master-username $(RDS_USER) \
		--master-user-password $(RDS_PASSWORD) \
		--scaling-configuration MinCapacity=1,MaxCapacity=2,AutoPause=true \
		--vpc-security-group-ids $(RDS_SECURITY_GROUP_IDS)
