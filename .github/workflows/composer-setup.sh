#!/bin/bash
# Configures Composer authentication and repositories for /tmp/magento.
# Expects secrets in the environment: GITLAB_TOKEN, LOKI_REPO_USERNAME, LOKI_REPO_PASSWORD,
# MAGENTO_MARKETPLACE_PUBLIC_KEY, MAGENTO_MARKETPLACE_PRIVATE_KEY, GITHUB_TOKEN.
# Optional: MAGENTO_VERSION, WITH_FUNCTIONAL_TEST_REPOS=1, VALIDATE_GITHUB_TOKEN=1, PACKAGE_SOURCE.
set -euo pipefail

PACKAGE_SOURCE="${PACKAGE_SOURCE:-${GITHUB_WORKSPACE:-/tmp/magento/package-source}}"

cd /tmp/magento

COMPOSER_AUTH_FILE="$(composer config --global home)/auth.json"
mkdir -p "$(dirname "$COMPOSER_AUTH_FILE")"
test -f "$COMPOSER_AUTH_FILE" || echo '{}' > "$COMPOSER_AUTH_FILE"
chmod 600 "$COMPOSER_AUTH_FILE"

composer_auth() {
  jq "$1" "$COMPOSER_AUTH_FILE" > "$COMPOSER_AUTH_FILE.tmp"
  chmod 600 "$COMPOSER_AUTH_FILE.tmp"
  mv "$COMPOSER_AUTH_FILE.tmp" "$COMPOSER_AUTH_FILE"
}

if [ -n "${GITHUB_TOKEN:-}" ]; then
  composer_auth '.["github-oauth"]["github.com"] = env.GITHUB_TOKEN'
fi

if [ "${VALIDATE_GITHUB_TOKEN:-0}" = "1" ]; then
  test -n "${GITHUB_TOKEN:-}" || { echo "::error::GitHub token is empty"; exit 1; }
  STATUS=$(printf 'header = "Authorization: Bearer %s"\n' "$GITHUB_TOKEN" | curl -sS -K - -o /tmp/github-rate-limit.json -w "%{http_code}" -H "Accept: application/vnd.github+json" https://api.github.com/rate_limit)
  echo "GitHub API status: $STATUS"
  test "$STATUS" = "200" || { echo "::error::GitHub token rejected (HTTP $STATUS)"; exit 1; }
  jq '.resources.core' /tmp/github-rate-limit.json
  test "$(jq '.resources.core.remaining' /tmp/github-rate-limit.json)" -gt 0 || { echo "::error::GitHub API rate limit exhausted"; exit 1; }
fi

if [ -n "${GITLAB_TOKEN:-}" ]; then
  composer config gitlab-domains gitlab.loki-extensions.com
  composer_auth '.["gitlab-token"]["gitlab.loki-extensions.com"] = env.GITLAB_TOKEN'
  composer config repositories.loki-extensions composer https://gitlab.loki-extensions.com/api/v4/group/loki-extensions/-/packages/composer/packages.json
  composer config repositories.loki-third-party composer https://gitlab.loki-extensions.com/api/v4/group/loki-third-party/-/packages/composer/packages.json
  composer config repositories.loki-checkout composer https://gitlab.loki-extensions.com/api/v4/group/loki-checkout/-/packages/composer/packages.json
  composer config repositories.loki-checkout-dev composer https://gitlab.loki-extensions.com/api/v4/group/loki-checkout-dev/-/packages/composer/packages.json
  if [ "${WITH_FUNCTIONAL_TEST_REPOS:-0}" = "1" ]; then
    composer config repositories.loki-functional-tests vcs https://gitlab.loki-extensions.com/loki-extensions/Loki_FunctionalTests.git
    composer config repositories.loki-checkout-functional-tests vcs https://gitlab.loki-extensions.com/loki-checkout-dev/LokiCheckout_FunctionalTests.git
  fi
fi

if [ -n "${LOKI_REPO_PASSWORD:-}" ]; then
  composer_auth '.["http-basic"]["private-packages.loki-extensions.com"] = {"username": (env.LOKI_REPO_USERNAME // ""), "password": env.LOKI_REPO_PASSWORD}'
  composer config repositories.mirror '{"type":"composer","url":"https://private-packages.loki-extensions.com/","canonical":false}'
fi

if [ -n "${MAGENTO_MARKETPLACE_PUBLIC_KEY:-}" ]; then
  composer_auth '.["http-basic"]["repo.magento.com"] = {"username": env.MAGENTO_MARKETPLACE_PUBLIC_KEY, "password": (env.MAGENTO_MARKETPLACE_PRIVATE_KEY // "")}'
fi

# Module-specific scripts may rely on unset variables
set +u

if [ -f "$PACKAGE_SOURCE/.github/workflows/composer-additional.sh" ]; then
  source "$PACKAGE_SOURCE/.github/workflows/composer-additional.sh"
fi

if [ -n "${MAGENTO_VERSION:-}" ] && [ -f "$PACKAGE_SOURCE/.github/workflows/composer-${MAGENTO_VERSION}.sh" ]; then
  source "$PACKAGE_SOURCE/.github/workflows/composer-${MAGENTO_VERSION}.sh"
fi
