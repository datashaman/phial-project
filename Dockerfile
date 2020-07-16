FROM lambci/lambda:build-provided

RUN yum update -y

RUN rpm --import https://download.fedoraproject.org/pub/epel/RPM-GPG-KEY-EPEL-7

RUN yum install -y \
    https://dl.fedoraproject.org/pub/epel/epel-release-latest-7.noarch.rpm \
    httpd-mmn \
    yum-utils

RUN yum install -y --disableplugin="priorities" \
    php73 \
    php73-gd \
    php73-json \
    php73-mbstring \
    php73-process \
    php73-xml

WORKDIR /opt

RUN mkdir bin \
    && cp /usr/bin/{phar,php} bin \
    && curl -sL https://getcomposer.org/installer | bin/php -- --install-dir=bin/ --filename=composer

RUN mkdir lib \
    && cp \
        /usr/lib64/libedit.so* \
        /usr/lib64/libncurses.so* \
        /usr/lib64/libpcre.so* \
        /usr/lib64/libtinfo.so* \
        lib

RUN mkdir lib/php \
    && cp -a /usr/lib64/php/7.3/modules lib/php
