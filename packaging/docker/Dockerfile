FROM php:7.4-apache
# Install PHP extensions and PECL modules.
ENV BUILDDEP=" \
        libbz2-dev \
        libsasl2-dev \
        libonig-dev \
        git \
        zip \
    "
RUN buildDeps="${BUILDDEP}" \
    runtimeDeps=" \
        libicu-dev \
        libldap2-dev \
        libzip-dev \
	locales \
	locales-all \
    " \
    && apt-get update && DEBIAN_FRONTEND=noninteractive apt-get install -y $buildDeps $runtimeDeps \
    && docker-php-ext-install bcmath bz2 iconv intl opcache \
    && docker-php-ext-configure ldap --with-libdir=lib/x86_64-linux-gnu/ \
    && docker-php-ext-install ldap \
    && echo en_US.UTF-8 UTF-8 >/etc/locale.gen \
    && /usr/sbin/locale-gen \
    && a2enmod rewrite
RUN mkdir -p /usr/share/php/smarty3/ && \
    curl -Lqs https://github.com/smarty-php/smarty/archive/v3.1.35.tar.gz | \
    tar xzf - -C /usr/share/php/smarty3/ --strip-components=2
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY . /var/www
RUN rmdir /var/www/html && \
    mv /var/www/htdocs /var/www/html && \
    mkdir -p /var/www/templates_c && \
    chown -R www-data: /var/www/templates_c
COPY --from=composer/composer:latest-bin /composer /usr/bin/composer
RUN cd /var/www && /usr/bin/composer install
RUN buildDeps="${BUILDDEP}" \
    && apt-get purge -y --auto-remove $buildDeps \
    && rm -r /var/lib/apt/lists/*
ENV LC_CTYPE=en_US.UTF-8
EXPOSE 80

