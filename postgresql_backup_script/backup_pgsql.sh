#!/bin/sh

# Location to place backups.
backup_dir="/db/pgsql/9.3/backups/"

#String to append to the name of the backup files
backup_date=`date +%Y%m%d`

#Numbers of days you want to keep copie of your databases
number_of_days=30

databases="***** postgres"
for i in $databases; do
  backup_file=$i\_$backup_date.sql
  echo Dumping $i to $backup_dir$backup_file
  /usr/bin/pg_dump -p 6432 -U ***** $i | gzip > $backup_dir$backup_file.gz

  # keep track of the latest backup via a symlink for easier downloading
  latest=${backup_dir}${i}.latest.sql.gz
  rm ${latest}
  ln -s ${backupd_dir}${backup_file}.gz ${latest}
done
find $backup_dir -type f -prune -mtime +$number_of_days -exec rm -f {} \;
