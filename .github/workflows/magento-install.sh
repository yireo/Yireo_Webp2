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
  --disable-modules="$DISABLE_MODULES"

bin/magento deploy:mode:set developer
bin/magento cache:disable full_page

cd /tmp/magento/pub/
nohup php -S 0.0.0.0:8888 >/tmp/php-server.log 2>&1 &

echo "Waiting for Magento front to become reachable on ${MAGENTO_BASE_URL}"
result=0
for i in {1..60}; do
  if curl -s "${MAGENTO_BASE_URL}" 2>1 >/dev/null; then
    echo " OK, available"; result=1; break;
  fi
  echo -n "."
  sleep 1
done
test $result -eq 0 && echo "Not available" && cat /tmp/php-server.log && exit 1
exit 0
