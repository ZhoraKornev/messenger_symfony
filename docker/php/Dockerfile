ARG ENV
ARG TIMEZONE

FROM php:8.0.13-fpm-buster

RUN apt-get update
RUN apt-get install -y \
    apt-utils \
    ca-certificates \
    apt-transport-https \
    software-properties-common \
    zlib1g-dev \
    libxml2-dev \
    libzip-dev \
    unzip \
    postgresql-server-dev-11 \
    gnupg2 \
    supervisor \
    libfreetype6-dev libjpeg62-turbo-dev libpng-dev \
    librabbitmq-dev

RUN curl -sL https://deb.nodesource.com/setup_12.x  | bash -
RUN apt-get -y install nodejs
RUN npm install

COPY config/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# Extract PHP source
# Create directory for amqp extension
# Download AMQP master branch files to extension directory
# Install amqp extension using built-in docker binary
RUN docker-php-source extract \
    && mkdir /usr/src/php/ext/amqp \
    && curl -L https://github.com/php-amqp/php-amqp/archive/master.tar.gz | tar -xzC /usr/src/php/ext/amqp --strip-components=1

RUN docker-php-ext-install \
    intl \
    pdo_pgsql \
    zip \
    opcache \
    amqp

RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install -j$(nproc) gd

RUN docker-php-ext-install amqp

ARG ENV

RUN if [ "$ENV" = "dev" ] ; then \
        pecl install xdebug-3.1.1 && \
        docker-php-ext-enable xdebug  \
    ; fi

RUN docker-php-source delete

COPY config/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini
COPY config/docker-php-memlimit.ini /usr/local/etc/php/conf.d/docker-php-memlimit.ini

COPY supervisor/* /etc/supervisor/conf.d/

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/bin --filename=composer --quiet

ARG TIMEZONE

RUN ln -snf /usr/share/zoneinfo/$TIMEZONE /etc/localtime && echo $TIMEZONE > /etc/timezone

WORKDIR /var/www/app

RUN usermod -u 1000 www-data

CMD ["/usr/bin/supervisord"]
