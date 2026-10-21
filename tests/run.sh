#!/bin/sh
# Runs tests/smoke.sh against each PHP version using the official docker images.
# usage: tests/run.sh [version ...]   e.g. tests/run.sh 5.6 8.5

cd "$(dirname "$0")/.." || exit 1

versions=${*:-"5.6 7.4 8.0 8.1 8.2 8.3 8.4 8.5"}
failed=""

for v in $versions; do
  docker run --rm -v "$PWD":/app -w /app "php:$v-cli" sh tests/smoke.sh || failed="$failed $v"
done

if [ -n "$failed" ]; then
  echo "FAILED on PHP:$failed"
  exit 1
fi
echo "All versions passed."
