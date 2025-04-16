FROM php:8.3.19-fpm

RUN groupadd -g "1000" app && \
    useradd -g "1000" -u "1000" -d /home/app -s /bin/bash app && \
    usermod -aG sudo app

RUN chsh -s /bin/bash app && \
    mkdir -p /home/app /sock /var/www/html /var/www/var/lock && \
    chown -R app:app /home/app /sock /var/www/html /var/www/var/lock

RUN apt-get update && apt-get install -y --no-install-recommends \
        apt-transport-https \
        bzip2 \
        ca-certificates \
        cron \
        default-mysql-client \
        freetds-bin \
        freetds-common \
        freetds-dev \
        freetype* \
        gnupg \
        iputils-ping \
        libfreetype6-dev \
        libicu-dev \
        libjpeg62-turbo-dev \
        libmagickwand-dev \
        libmcrypt-dev \
        libpng-dev \
        libssl-dev \
        libssl-dev \
        libsybdb5 \
        libxslt1-dev \
        libzip-dev \
        locales \
        locales-all \
        lsb-release \
        lynx \
        nano \
        procps \
        psmisc \
        sudo \
        supervisor \
        unzip \
        vim \
        webp \
        wget && \
    rm -rf /var/lib/apt/lists/*

RUN pecl channel-update pecl.php.net && \
    pecl install apcu imagick mongodb-1.14.0 redis xdebug && \
    pecl clear-cache && \
    echo "extension=mongodb.so" > /usr/local/etc/php/conf.d/ext-mongo.ini && \
    ln -sf /usr/lib/x86_64-linux-gnu/libsybdb.a /usr/lib/

RUN curl https://packages.sury.org/php/apt.gpg -o /etc/apt/trusted.gpg.d/php.gpg &&  \
    echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" >> /etc/apt/sources.list.d/php.list && \
    apt-get update && apt-get install -y libsodium-dev libsodium23

RUN docker-php-ext-configure gd --with-jpeg --with-freetype --with-webp && \
    docker-php-ext-install \
        bcmath \
        ctype \
        dom \
        gd \
        iconv \
        intl \
        pdo_mysql \
        opcache \
        pcntl \
        pdo_dblib \
        simplexml \
        soap \
        sockets \
        sodium \
        zip \
        xml \
        xsl && \
    docker-php-ext-enable apcu redis imagick xdebug

RUN curl -sS https://getcomposer.org/installer | \
    php -- --install-dir=/usr/local/bin --filename=composer --version=2.2.22

WORKDIR /var/www/html
USER app:app
