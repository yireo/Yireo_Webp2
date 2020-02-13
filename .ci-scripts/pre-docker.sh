#!/bin/bash
apt-get update
apt-get install -y git-core mariadb-client \
    libfreetype6-dev libjpeg62-turbo-dev libpng-dev \
    unzip libzip-dev zlib1g-dev libxslt-dev

cat <<EOF > /etc/my.cnf
[client]
host = mysql
user = root
EOF

curl -so composer https://getcomposer.org/composer-stable.phar
mv composer /usr/local/bin/composer
chmod 755 /usr/local/bin/composer

docker-php-ext-configure gd
docker-php-ext-install -j$(nproc) gd
docker-php-ext-install zip bcmath xsl intl pdo_mysql soap sockets
docker-php-ext-enable gd zip bcmath xsl intl pdo_mysql soap sockets sodium
