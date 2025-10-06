#!/bin/bash

# Simple script to backup production database
# Save this as /mnt/user/appdata/ktl-booking/backup-db.sh on Unraid
# Then just run: ./backup-db.sh

cd /mnt/user/appdata/ktl-booking
git pull
./unraid-export-db.sh
