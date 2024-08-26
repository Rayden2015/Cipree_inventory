#!/bin/bash

# Define the directory where your SQL files are located
SQL_DIR="."

# Define the directory where you want to store the backups
BACKUP_DIR="."

# Function to calculate the date two days ago (assuming gdate is available)
calculate_date_two_days_ago() {
  DATE_2_DAYS_AGO=$(date -d "2 days ago" +"%Y-%m-%d")
  # Check if gdate command succeeded (exit code 0 indicates success)
  if [[ $? -ne 0 ]]; then
    log_entry "Error: Failed to calculate date using gdate."
    exit 1  # Exit script with error code 1
  fi
}

# Function to write log messages
log_entry() {
  echo "$(date +"%Y-%m-%d %H:%M:%S") - $1" >> "$LOG_FILE"
}

# Start logging
LOG_FILE="${BACKUP_DIR}/daily_zipping.log"
log_entry "Backup process started"

# Calculate the date two days ago
calculate_date_two_days_ago

# Define the backup file pattern
BACKUP_FILE="pensmqhz_inx_ventory-${DATE_2_DAYS_AGO}*.sql"

# Zip the backup files
zip_files() {
  zip -r ${BACKUP_DIR}/pensmqhz_inx_ventory-${DATE_2_DAYS_AGO}.zip . -i ${SQL_DIR}/${BACKUP_FILE} >> $LOG_FILE 2>&1
  if [[ $? -ne 0 ]]; then
    log_entry "Error: Failed to create zip archive."
    exit 1  # Exit script with error code 1
  fi
}

# Function to remove original SQL files
remove_files() {
   #echo $BACKUP_FILE
  rm -rf ${BACKUP_FILE} >> $LOG_FILE 2>&1
#    rm -rf ./pensmqhz_inx_ventory-2024-05-08* 
  if [[ $? -ne 0 ]]; then
    log_entry "Error: Failed to delete original SQL files."
    exit 1  # Exit script with error code 1
  fi
}

# Zip the files
zip_files

# Log completion
log_entry "Backup process completed"

# Log files deletion started
log_entry "Backup files deletion started"

# Remove the original SQL files
remove_files

# Backup files deletion completed successfully
log_entry "Backup files deletion completed successfully"
