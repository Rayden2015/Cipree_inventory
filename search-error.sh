#!/bin/bash
# Quick script to search error logs by error ID

if [ -z "$1" ]; then
    echo "Usage: ./search-error.sh <error_id>"
    echo "Example: ./search-error.sh 762253241"
    exit 1
fi

ERROR_ID=$1
LOG_FILE="storage/logs/errors/error.log"

echo "================================"
echo "Searching for Error ID: $ERROR_ID"
echo "================================"
echo

if [ ! -f "$LOG_FILE" ]; then
    echo "❌ Error log file not found: $LOG_FILE"
    exit 1
fi

# Search for the error ID
RESULT=$(grep -A 50 "$ERROR_ID" "$LOG_FILE")

if [ -z "$RESULT" ]; then
    echo "❌ Error ID $ERROR_ID not found in log files"
    echo
    echo "Tips:"
    echo "  - Check if the error ID is correct"
    echo "  - Error may be in the database (check via web UI)"
    echo "  - Log files may have been rotated"
else
    echo "✅ Found Error ID $ERROR_ID:"
    echo
    echo "$RESULT"
fi

echo
echo "================================"
echo "Search complete"
echo "================================"

