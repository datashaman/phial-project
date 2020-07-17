#!/usr/bin/env bash

set -e

DIR="$(dirname "${BASH_SOURCE[0]}")"

: "${ARTIFACTS_DIR:=/tmp/artifacts}"

if [ -e "${ARTIFACTS_DIR}" ]; then
    rm -rf "${ARTIFACTS_DIR}"
fi

mkdir -p "${ARTIFACTS_DIR}"

cp -a app bootstrap* composer.* config.php container.php "${ARTIFACTS_DIR}"
# composer install --no-dev --optimize-autoloader --working-dir="${ARTIFACTS_DIR}"
composer install --working-dir="${ARTIFACTS_DIR}"
