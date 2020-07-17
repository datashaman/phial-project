ARG PHP_PACKAGE

FROM "datashaman/phial-runtime:build-${PHP_PACKAGE}"

ARG PHP_PACKAGE
ENV LD_LIBRARY_PATH="/opt/php74/lib:$LD_LIBRARY_PATH"
ENV PATH="/opt/php74/bin:$PATH"

COPY php.ini /opt/${PHP_PACKAGE}/etc/php.d/phial.ini
