#!/bin/bash
echo "Waiting for MySQL (${MYSQL_HOST}:${MYSQL_PORT})"
for i in {1..120}; do
  if mariadb-admin ping -h"${MYSQL_HOST}" -P"${MYSQL_PORT}" --silent; then
    echo "OK"; break
  fi
  echo -n "."
  sleep 1
done

cat <<EOF > ~/.my.cnf
[client-mariadb]
disable-ssl
EOF

echo "Waiting for OpenSearch (${OPENSEARCH_HOST}:${OPENSEARCH_PORT})"
ready=0
for i in {1..120}; do
  if curl -s "http://${OPENSEARCH_HOST}:${OPENSEARCH_PORT}" | grep -q '"tagline"'; then
    echo "OK"
    ready=1
    break
  fi
  echo -n "."
  sleep 1
done

if [ "${ready:-0}" -ne 1 ]; then
  echo "OpenSearch NOT OK, exiting"
  exit 1
fi

DISABLE_MODULES=`php $GITHUB_WORKSPACE/.github/workflows/get-modules.php disable-graphql=$DISABLE_GRAPHQL disable-inventory=$DISABLE_INVENTORY disable-adobe=$DISABLE_ADOBE`
 
SECURE_ARGS=()
case "${MAGENTO_BASE_URL}" in
  https://*)
    USE_HTTPS=1
    SECURE_ARGS=(--base-url-secure="${MAGENTO_BASE_URL}" --use-secure=1 --use-secure-admin=1)
    ;;
  *) USE_HTTPS=0 ;;
esac

cd /tmp/magento
php -dmemory_limit=-1 bin/magento setup:install \
  --base-url="${MAGENTO_BASE_URL}" \
  --db-host="${MYSQL_HOST}" \
  --db-name="${MYSQL_DATABASE}" \
  --db-user="${MYSQL_USER}" \
  --db-password="${MYSQL_PASSWORD}" \
  --backend-frontname="${MAGENTO_BACKEND_FRONTNAME}" \
  --admin-firstname="${MAGENTO_ADMIN_FIRSTNAME}" \
  --admin-lastname="${MAGENTO_ADMIN_LASTNAME}" \
  --admin-email="${MAGENTO_ADMIN_EMAIL}" \
  --admin-user="${MAGENTO_ADMIN_USER}" \
  --admin-password="${MAGENTO_ADMIN_PASSWORD}" \
  --language="${MAGENTO_LANGUAGE}" \
  --currency="${MAGENTO_CURRENCY}" \
  --timezone="${MAGENTO_TIMEZONE}" \
  --use-rewrites="${MAGENTO_USE_REWRITES}" \
  --search-engine="${MAGENTO_SEARCH_ENGINE}" \
  --opensearch-host="${OPENSEARCH_HOST}" \
  --opensearch-port="${OPENSEARCH_PORT}" \
  --session-save=redis \
  --session-save-redis-host=redis \
  --session-save-redis-port=6379 \
  --cache-backend-redis-server=redis \
  --cache-backend-redis-port=6379 \
  "${SECURE_ARGS[@]}" \
  --disable-modules="$DISABLE_MODULES"

bin/magento deploy:mode:set developer
bin/magento cache:disable full_page

cd /tmp/magento/pub/
if [ "$USE_HTTPS" -eq 1 ]; then
  nohup php -S 127.0.0.1:8080 -t /tmp/magento/pub $GITHUB_WORKSPACE/.github/workflows/php-server-router.php >/tmp/php-server.log 2>&1 &

  HTTPS_HOST_PORT="${MAGENTO_BASE_URL#https://}"
  HTTPS_HOST_PORT="${HTTPS_HOST_PORT%%/*}"
  HTTPS_PORT="${HTTPS_HOST_PORT##*:}"
  test "$HTTPS_PORT" = "$HTTPS_HOST_PORT" && HTTPS_PORT=443

  openssl req -x509 -nodes -newkey rsa:2048 -days 7 \
    -keyout /tmp/magento-tls.key -out /tmp/magento-tls.crt \
    -subj "/CN=localhost" \
    -addext "subjectAltName=DNS:localhost,DNS:php,IP:127.0.0.1"
  cat /tmp/magento-tls.key /tmp/magento-tls.crt > /tmp/magento-tls.pem

  nohup socat OPENSSL-LISTEN:${HTTPS_PORT},fork,reuseaddr,cert=/tmp/magento-tls.pem,verify=0 TCP:127.0.0.1:8080 >/tmp/socat.log 2>&1 &
else
  nohup php -S 0.0.0.0:8888 -t /tmp/magento/pub $GITHUB_WORKSPACE/.github/workflows/php-server-router.php >/tmp/php-server.log 2>&1 &
fi

echo "Waiting for Magento front to become reachable on ${MAGENTO_BASE_URL}"
result=0
for i in {1..60}; do
  if curl -k -s "${MAGENTO_BASE_URL}" >/dev/null 2>&1; then
    echo " OK, available"; result=1; break;
  fi
  echo -n "."
  sleep 1
done
if [ $result -eq 0 ]; then
  echo "Not available"
  cat /tmp/php-server.log
  test -f /tmp/socat.log && cat /tmp/socat.log
  exit 1
fi
exit 0
