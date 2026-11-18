#!/bin/bash
# Configures Composer authentication and repositories for /tmp/magento.
# Expects secrets in the environment: GITLAB_TOKEN, LOKI_REPO_USERNAME, LOKI_REPO_PASSWORD,
# MAGENTO_MARKETPLACE_PUBLIC_KEY, MAGENTO_MARKETPLACE_PRIVATE_KEY, GITHUB_TOKEN.
# Optional: MAGENTO_VERSION, WITH_FUNCTIONAL_TEST_REPOS=1, VALIDATE_GITHUB_TOKEN=1, PACKAGE_SOURCE.
set -euo pipefail

PACKAGE_SOURCE="${PACKAGE_SOURCE:-${GITHUB_WORKSPACE:-/tmp/magento/package-source}}"

cd /tmp/magento

if [ -n "${GITHUB_TOKEN:-}" ]; then
  composer config --global --auth github-oauth.github.com "$GITHUB_TOKEN"
fi

if [ "${VALIDATE_GITHUB_TOKEN:-0}" = "1" ]; then
  test -n "${GITHUB_TOKEN:-}" || { echo "::error::GitHub token is empty"; exit 1; }
  STATUS=$(curl -sS -o /tmp/github-rate-limit.json -w "%{http_code}" -H "Authorization: Bearer $GITHUB_TOKEN" -H "Accept: application/vnd.github+json" https://api.github.com/rate_limit)
  echo "GitHub API status: $STATUS"
  test "$STATUS" = "200" || { echo "::error::GitHub token rejected (HTTP $STATUS)"; exit 1; }
  jq '.resources.core' /tmp/github-rate-limit.json
  test "$(jq '.resources.core.remaining' /tmp/github-rate-limit.json)" -gt 0 || { echo "::error::GitHub API rate limit exhausted"; exit 1; }
fi

if [ -n "${GITLAB_TOKEN:-}" ]; then
  composer config gitlab-domains gitlab.yireo.com
  composer config --global --auth gitlab-token.gitlab.yireo.com "$GITLAB_TOKEN"
  composer config repositories.loki-extensions composer https://gitlab.yireo.com/api/v4/group/loki-extensions/-/packages/composer/packages.json
  composer config repositories.loki-third-party composer https://gitlab.yireo.com/api/v4/group/loki-third-party/-/packages/composer/packages.json
  composer config repositories.loki-checkout composer https://gitlab.yireo.com/api/v4/group/loki-checkout/-/packages/composer/packages.json
  composer config repositories.loki-checkout-dev composer https://gitlab.yireo.com/api/v4/group/loki-checkout-dev/-/packages/composer/packages.json
  if [ "${WITH_FUNCTIONAL_TEST_REPOS:-0}" = "1" ]; then
    composer config repositories.loki-functional-tests vcs https://gitlab.yireo.com/loki-extensions/Loki_FunctionalTests.git
    composer config repositories.loki-checkout-functional-tests vcs https://gitlab.yireo.com/loki-checkout-dev/LokiCheckout_FunctionalTests.git
  fi
fi

if [ -n "${LOKI_REPO_PASSWORD:-}" ]; then
  composer config --global --auth http-basic.private-packages.loki-extensions.com "${LOKI_REPO_USERNAME:-}" "$LOKI_REPO_PASSWORD"
  composer config repositories.mirror '{"type":"composer","url":"https://private-packages.loki-extensions.com/","canonical":false}'
fi

if [ -n "${MAGENTO_MARKETPLACE_PUBLIC_KEY:-}" ]; then
  composer config --global --auth http-basic.repo.magento.com "$MAGENTO_MARKETPLACE_PUBLIC_KEY" "${MAGENTO_MARKETPLACE_PRIVATE_KEY:-}"
fi

# Module-specific scripts may rely on unset variables
set +u

if [ -f "$PACKAGE_SOURCE/.github/workflows/composer-additional.sh" ]; then
  source "$PACKAGE_SOURCE/.github/workflows/composer-additional.sh"
fi

if [ -n "${MAGENTO_VERSION:-}" ] && [ -f "$PACKAGE_SOURCE/.github/workflows/composer-${MAGENTO_VERSION}.sh" ]; then
  source "$PACKAGE_SOURCE/.github/workflows/composer-${MAGENTO_VERSION}.sh"
fi
