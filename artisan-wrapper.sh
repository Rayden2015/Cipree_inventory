#!/bin/bash
# Wrapper script to run artisan with project php.ini
# This suppresses PHP 8.4 deprecation warnings from vendor packages

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
php -c "$SCRIPT_DIR/php.ini" "$SCRIPT_DIR/artisan" "$@"

