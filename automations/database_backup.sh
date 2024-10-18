#!/bin/bash

# MySQL database information
DB_NAME="pensmqhz_inx_ventory"
DB_USER="pensmqhz_inventory_user"
DB_PASSWORD="9FmxnjG-IYm0"
DB_HOST="127.0.0.1"
DB_PORT="3306"  # Port number

# Get the current date and time
CURRENT_TIME=$(date +"%Y-%m-%d_%H-%M-%S")

# Output backup file with DB name and current time
OUTPUT_FILE="$DB_NAME-$CURRENT_TIME.sql"

# Error log file
ERROR_FILE="backup_error.log"

# Path to mysqldump executable (you may need to provide the full path)
MYSQLDUMP="mysqldump"

# Dump the database and capture errors in the error file
$MYSQLDUMP -h $DB_HOST -u $DB_USER -p$DB_PASSWORD -P$DB_PORT $DB_NAME > $OUTPUT_FILE 2> $ERROR_FILE

# Check if mysqldump was successful
if [ $? -eq 0 ]; then
  echo "Backup of $DB_NAME completed successfully. File: $OUTPUT_FILE"
else
  echo "Backup of $DB_NAME failed. Check the error message in $ERROR_FILE."
fi
