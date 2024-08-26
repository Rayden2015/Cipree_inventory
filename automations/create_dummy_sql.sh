#!/bin/bash

# Define the directory where you want to create the files
FILE_DIR="."

# Calculate the date two days ago
DATE_2_DAYS_AGO=$(gdate -d "2 days ago" +"%Y-%m-%d")

# Loop to create 100 files
for ((i = 0; i < 100; i++)); do
    # Calculate the date for the current iteration
    CURRENT_DATE=$(gdate -d "${DATE_2_DAYS_AGO}" +"%Y-%m-%d")
    
    # Create a sample file with the calculated date in the name
    touch "${FILE_DIR}/pensmqhz_inx_ventory-${CURRENT_DATE}-$i.sql"
done
