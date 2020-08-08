ARG PHP_PACKAGE

FROM "datashaman/phial-runtime:build-${PHP_PACKAGE}"

ARG PHP_PACKAGE

COPY php.ini /opt/${PHP_PACKAGE}/etc/php.d/phial-project.ini
