#!/bin/sh

# Location to place backups.
backup_dir="/data/backups_mongodb/"

#String to append to the name of the backup files
backup_date=`date +%Y%m%d`

#Numbers of days you want to keep copie of your databases
number_of_days=30

backup_file_prefix="mongodb_backup"


#find ${backup_dir}* -type d -ctime +${number_of_days} -exec /bin/rm -rf {} \;
find $backup_dir -type f -prune -mtime +$number_of_days -exec rm -f {} \;

backup_dir_date=${backup_dir}${backup_date}

databases="*****"
for i in $databases; do
  echo
  echo ..... Dumping $i to $backup_dir_date
  echo
  /bin/mongodump -u root -p ***** --authenticationDatabase admin --db $i --out $backup_dir_date
done

backup_file=${backup_dir}${backup_file_prefix}_${backup_date}.tar.gz

echo
echo ..... Creating tar zip file of ${backup_dir_date}: ${backup_file}
echo
/bin/tar cvzf ${backup_file} ${backup_dir_date}

echo
echo ..... Deleting ${backup_dir_date}.
echo
/bin/rm -rf ${backup_dir_date}

# keep track of the latest backup via a symlink for easier downloading
latest=${backup_dir}${backup_file_prefix}.latest.tar.gz
echo
echo ..... Deleting ${latest}
/bin/rm -f ${latest}
echo ..... Creating ${latest}
echo
/bin/ln -s ${backup_file} ${latest}

echo
echo ..... Success
echo
