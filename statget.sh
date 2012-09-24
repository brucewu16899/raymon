#!/bin/bash
DATETIME=`date +%s`
DATEHOUR=`date +%H`
HOURFILE=`cat /var/www/status/.hour`

if [ ! -d ./history/ ]; then
    mkdir -p ./history/
fi

if [ ! -f /var/www/status/.hour ]; then
    echo $DATEHOUR > /var/www/status.hour
fi


for i in `find /var/www/status/ -maxdepth 1 -type f -iname "*.json"`; do 
    J=`basename "${i##*/}"`
    cp ${i} /var/www/status/${J}.old
done


wget --quiet -O /var/www/status/raymii.org.json http://69.197.183.203/stat.json 
wget --quiet -O /var/www/status/raymii.nl.json http://raymii.nl/stat.json
wget --quiet -O /var/www/status/vps1.spcs.json http://vps1.sparklingclouds.nl/stat.json
wget --quiet -O /var/www/status/vps3.spcs.json http://vps3.sparklingclouds.nl/stat.json
wget --quiet -O /var/www/status/vps5.spcs.json http://vps5.sparklingclouds.nl/stat.json
wget --quiet -O /var/www/status/vps6.spcs.json http://vps6.sparklingclouds.nl/stat.json
wget --quiet -O /var/www/status/vps8.spcs.json http://vps8.sparklingclouds.nl/stat.json
wget --quiet -O /var/www/status/vps11.spcs.json http://vps11.sparklingclouds.nl/stat.json
wget --quiet -O /var/www/status/vps12.spcs.json http://vps12.sparklingclouds.nl/stat.json
wget --quiet -O /var/www/status/vps13.spcs.json http://vps13.sparklingclouds.nl/stat.json
wget --quiet -O /var/www/status/vps14.spcs.json http://vps14.sparklingclouds.nl/stat.json

if [[ ${DATEHOUR} != ${HOURFILE} ]]; then
    for i in `find /var/www/status/ -maxdepth 1 -type f -iname "*.json"`; do 
            J="${i}.old"
            if [ -f ${J} ]; then
                    mdI=`md5sum ${i} | awk '{ print $1 }'`
                    mdJ=`md5sum ${J} | awk '{ print $1 }'`
                    K=`basename "${i##*/}"`
                    if [ "${mdI}" != "${mdJ}" ]; then
                        cp ${i} /var/www/status/history/${K}.${DATETIME}
                        chmod 777 -R /var/www/status/history/
                    fi
            fi
    done
fi   
echo $DATEHOUR > /var/www/status/.hour