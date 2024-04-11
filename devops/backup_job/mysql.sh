#!/bin/bash

echo "[Default]" > /$HOME/.s3cfg
        echo "access_key = $ACCESS_KEY" >> /$HOME/.s3cfg
        echo "secret_key = $SECRET_KEY" >> /$HOME/.s3cfg
        echo "host_base = $ENDPOINT" >> /$HOME/.s3cfg
        echo "host_bucket = $ENDPOINT" >> /$HOME/.s3cfg
        echo "enable_multipart = True" >> /$HOME/.s3cfg
        echo "multipart_chunk_size_mb = 15" >> /$HOME/.s3cfg
        echo "use_https = True" >> /$HOME/.s3cfg

mkdir /var/lib/backup

date=`TZ='Iran' date +'%H-%M_%d-%m-%Y'`
mysqldump -u root -p$db_password -h $target_srv_name $db_name > /var/lib/backup/db_backup_"$target_srv_name"_"$date".sql

cd /var/lib/backup/ && \
tar -cJvf db_backup_"$target_srv_name"_"$date".tar.xz db_backup_"$target_srv_name"_"$date".sql

tar -tf /var/lib/backup/db_backup_"$target_srv_name"_"$date".tar.xz && \
s3cmd put /var/lib/backup/db_backup_"$target_srv_name"_"$date".tar.xz s3://$BUCKETNAME/$region_name/$namespace/$target_srv_name/db_backup_"$target_srv_name"_"$date".tar.xz
