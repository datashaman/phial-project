#!/usr/bin/env bash

: "${ARTIFACTS_DIR:=/tmp/artifacts}"

if [ -e "${ARTIFACTS_DIR}" ]; then
    rm -rf "${ARTIFACTS_DIR}"
fi

mkdir -p "${ARTIFACTS_DIR}"

docker build --build-arg PHP_PACKAGE -t phial-project .

CONTAINER_ID=$(docker run --detach --tty phial-project bash)

docker cp "${CONTAINER_ID}:/opt/${PHP_PACKAGE}" "${ARTIFACTS_DIR}"

docker rm --force ${CONTAINER_ID}
