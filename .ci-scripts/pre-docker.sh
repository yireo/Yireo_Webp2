#!/bin/bash
apt-get update
apt-get install -y --no-install-recommends \
    git-core mariadb-client \
    libjpeg62-turbo-dev libpng-dev libjpeg-dev \
    unzip libzip-dev libxslt-dev zlib1g-dev libfreetype6-dev \
    multiarch-support \
    webp || exit 1

cat <<EOF > /etc/my.cnf
[client]
host = mysql
user = root
EOF

curl --output libjpeg-turbo8_1.4.2-0ubuntu3_amd64.deb http://archive.ubuntu.com/ubuntu/pool/main/libj/libjpeg-turbo/libjpeg-turbo8_1.4.2-0ubuntu3_amd64.deb
dpkg -i libjpeg-turbo8_1.4.2-0ubuntu3_amd64.deb || exit 1

curl --output libpng12-0_1.2.54-1ubuntu1.1_amd64.deb http://security.ubuntu.com/ubuntu/pool/main/libp/libpng/libpng12-0_1.2.54-1ubuntu1.1_amd64.deb
dpkg -i libpng12-0_1.2.54-1ubuntu1.1_amd64.deb || exit 1

curl -so composer https://getcomposer.org/composer-stable.phar
mv composer /usr/local/bin/composer
chmod 755 /usr/local/bin/composer

docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include
docker-php-ext-install -j$(nproc) gd
docker-php-ext-enable gd || exit 1

docker-php-ext-install zip bcmath xsl intl pdo_mysql soap sockets
