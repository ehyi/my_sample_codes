#!/bin/sh

# Location to place backups.
backup_dir="/home/eyi/mysql_backups/"

#String to append to the name of the backup files
backup_date=`date +%Y%m%d`

#Numbers of days you want to keep copie of your databases
number_of_days=30

databases="mysql *****"
for i in $databases; do
  backup_file=$i\_$backup_date.sql
  /bin/echo Dumping $i to $backup_dir$backup_file
  /bin/mysqldump -u ***** --opt --routines --databases $i | gzip > $backup_dir$backup_file.gz

  # keep track of the latest backup via a symlink for easier downloading
  latest=${backup_dir}${i}.latest.sql.gz
  /bin/rm ${latest}
  /bin/ln -s ${backupd_dir}${backup_file}.gz ${latest}
done
/bin/find $backup_dir -type f -prune -mtime +$number_of_days -exec rm -f {} \;

