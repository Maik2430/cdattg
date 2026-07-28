#!/usr/bin/env bash
# Cobertura PCOV en Docker.
# La DB va a /tmp (FS nativo del contenedor) para evitar locks en bind-mount Windows.
set -euo pipefail

cd /app

install_pcov_if_needed() {
    if php -m 2>/dev/null | grep -q '^pcov$'; then
        echo "PCOV already available"
        return 0
    fi

    echo "Installing PHP extensions + PCOV..."
    apt-get update -qq
    DEBIAN_FRONTEND=noninteractive apt-get install -y -qq \
        git unzip zip libzip-dev libsqlite3-dev libicu-dev libonig-dev \
        autoconf g++ make > /dev/null

    docker-php-ext-install pdo_sqlite zip intl mbstring bcmath > /dev/null 2>&1 || true
    pecl install pcov > /dev/null 2>&1 || pecl install pcov
    docker-php-ext-enable pcov > /dev/null
}

install_pcov_if_needed
php -m | grep -q pcov

mkdir -p storage/coverage storage/logs bootstrap/cache database 2>/dev/null || true

# Nunca usar /app/database/*.sqlite en Docker Desktop + Windows (locks/corrupción).
COVERAGE_DB="${COVERAGE_DB:-/tmp/cdattg_testing_coverage.sqlite}"
case "${COVERAGE_DB}" in
    /app/*|database/*|./database/*)
        echo "WARN: COVERAGE_DB=${COVERAGE_DB} está en el bind-mount; forzando /tmp/cdattg_testing_coverage.sqlite"
        COVERAGE_DB="/tmp/cdattg_testing_coverage.sqlite"
        ;;
esac

rm -f "${COVERAGE_DB}" "${COVERAGE_DB}-journal" "${COVERAGE_DB}-wal" "${COVERAGE_DB}-shm"
touch "${COVERAGE_DB}"
chmod 666 "${COVERAGE_DB}" 2>/dev/null || true

export DB_CONNECTION=sqlite
export DB_DATABASE="${COVERAGE_DB}"
export TESTING_DB="${COVERAGE_DB}"
export DB_FOREIGN_KEYS=true
export APP_ENV=testing

ARGS=(--coverage-text --colors=never)

if [ "${COVERAGE_HTML:-0}" = "1" ]; then
    ARGS+=(--coverage-html storage/coverage)
fi

# shellcheck disable=SC2206
TEST_TARGETS=(${COVERAGE_TESTS:-})

echo "PCOV coverage DB: ${COVERAGE_DB}"
echo "Targets: ${TEST_TARGETS[*]:-<full suite>}"

set +e
php -d memory_limit=1G -d pcov.enabled=1 vendor/bin/phpunit "${ARGS[@]}" "${TEST_TARGETS[@]}"
STATUS=$?
set -e

if [ "${COVERAGE_HTML:-0}" = "1" ]; then
    echo ""
    echo "Reporte HTML: storage/coverage/index.html"
fi

exit "${STATUS}"
