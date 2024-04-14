from mysql:5.7

run apt-get update && apt-get install python git python3-pip curl iputils-ping wget net-tools -y
run pip3 install git+https://github.com/s3tools/s3cmd.git@v2.1.0
copy mysql.sh /entrypoint.sh
run chmod +x /entrypoint.sh

entrypoint ["/bin/bash","/entrypoint.sh"]