ARG PHP_PACKAGE

FROM "datashaman/phial-runtime:build-${PHP_PACKAGE}"

ARG PHP_PACKAGE
ENV LD_LIBRARY_PATH="/opt/${PHP_PACKAGE}/lib:$LD_LIBRARY_PATH"
ENV PATH="/opt/${PHP_PACKAGE}/bin:$PATH"

COPY php.ini /opt/${PHP_PACKAGE}/etc/php.d/phial-project.ini
RUN sed -i 's/\${PHP_PACKAGE}/${PHP_PACKAGE}/' /opt/${PHP_PACKAGE}/etc/php.d/phial-project.ini
