#!/usr/bin/env bash
# Smoke mínimo de PCOV (usa la misma lógica que run-coverage.sh).
set -euo pipefail

export COVERAGE_TESTS="${COVERAGE_TESTS:-tests/Unit/ExampleTest.php}"
export COVERAGE_DB="${COVERAGE_DB:-/tmp/cdattg_testing_coverage.sqlite}"

exec bash /app/tools/run-coverage.sh
