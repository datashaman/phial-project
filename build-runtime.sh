#!/usr/bin/env bash

: "${ARTIFACTS_DIR:=/tmp/artifacts}"

if [ -e "${ARTIFACTS_DIR}" ]; then
    rm -rf "${ARTIFACTS_DIR}"
fi

mkdir -p "${ARTIFACTS_DIR}"

docker build -t phial-project .

CONTAINER_ID=$(docker run --detach --tty phial-project bash)

docker cp "${CONTAINER_ID}:/opt/bin" "${ARTIFACTS_DIR}/bin"
docker cp "${CONTAINER_ID}:/opt/lib" "${ARTIFACTS_DIR}/lib"

docker rm --force ${CONTAINER_ID}
