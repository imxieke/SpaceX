#!/usr/bin/env bash
# libraries qqwry maxmind geolite
CPATH='/tmp'
IPPATH=${CPATH}/ip
START_TIME=$(date +%s)
TIME=$(date +%Y%m%d)
mkdir  -p ${IPPATH}

GIT='https://github.com/lionsoul2014/ip2region'
IP2REGION_REGION='https://raw.githubusercontent.com/lionsoul2014/ip2region/master/data/global_region.csv'
IP2REGION_IPFILE='https://github.com/lionsoul2014/ip2region/raw/master/data/ip.merge.txt'
IP2REGION_APP_URL='https://github.com/lionsoul2014/ip2region/raw/master/maker/java/dbMaker-1.2.2.jar'

ARIN='http://ftp.arin.net/pub/stats/arin/delegated-arin-extended-latest'
APNIC='http://ftp.apnic.net/apnic/stats/apnic/delegated-apnic-latest'
RIPE='http://ftp.ripe.net/ripe/stats/delegated-ripencc-latest'
LACNIC='http://ftp.lacnic.net/pub/stats/lacnic/delegated-lacnic-latest'
AFRINIC='http://ftp.afrinic.net/pub/stats/afrinic/delegated-afrinic-latest'

MAXMIND_LICENSE_KEY='zWy6q40weZVZzWuv'
ASN='http://bgp.potaroo.net/as1221/asnames.txt'

LOGS='/tmp/mirrors.log'

OLD=$(pwd)/old

# Maxmind Start
maxmind()
{
    SAVETO='geolite2'
    mkdir -p ${SAVETO}
    LICENSE_KEY='zWy6q40weZVZzWuv'
    BIN="https://download.maxmind.com/app/geoip_download?license_key=${LICENSE_KEY}&suffix=tar.gz&edition_id="
    BIN256="https://download.maxmind.com/app/geoip_download?license_key=${LICENSE_KEY}&suffix=tar.gz.sha256&edition_id="
    CSV="https://download.maxmind.com/app/geoip_download?license_key=${LICENSE_KEY}&suffix=zip&edition_id="
    CSV256="https://download.maxmind.com/app/geoip_download?license_key=${LICENSE_KEY}&suffix=zip.sha256&edition_id="

    BIN_EDITION=(GeoLite2-City GeoLite2-Country GeoLite2-ASN)
    CSV_EDITION=(GeoLite2-City-CSV GeoLite2-Country-CSV GeoLite2-ASN-CSV)

    for NAME in ${BIN_EDITION[*]}; do
        if [[ ! -f ${SAVETO}/${NAME}-${TIME}.tar.gz ]]; then
            echo 'Fetch ' ${NAME}
            wget ${BIN}${NAME}    -O ${SAVETO}/${NAME}-${TIME}.tar.gz
            wget ${BIN256}${NAME} -O ${SAVETO}/${NAME}-${TIME}.tar.gz.sha256
            upload ${SAVETO}/${NAME}-${TIME}.tar.gz
            upload ${SAVETO}/${NAME}-${TIME}.tar.gz.sha256
        fi
    done

    for NAME in ${CSV_EDITION[*]}; do
        if [[ ! -f ${SAVETO}/${NAME}-${TIME}.zip ]]; then
            echo 'Fetch ' ${NAME}
            wget ${CSV}${NAME}    -O ${SAVETO}/${NAME}-${TIME}.zip
            wget ${CSV256}${NAME} -O ${SAVETO}/${NAME}-${TIME}.zip.sha256
            upload ${SAVETO}/${NAME}-${TIME}.zip
            upload ${SAVETO}/${NAME}-${TIME}.zip.sha256
        fi
    done

    echo ${TIME} 'Maxmind All done'
}

# Maxmind End

qqwry()
{
    QQWRY_FILE=qqwry-${TIME}.dat
    QQWRY_PATH=${IPPATH}/qqwry

    echo "Update qqwry.dat"
    SRC_FILE='http://update.cz88.net/soft/setup.zip'
    TIME=$(date +%Y%m%d)
    FILE_NAME=setup-${TIME}.zip

    if [[ $(command -v innoextract) == '' ]]; then
        echo 'innoextract Not install, Start install'
        sudo apt install -y innoextract
    fi

    mkdir -p ${QQWRY_PATH}
    cd ${QQWRY_PATH}

    if [[ ! -f ${FILE_NAME} ]]; then
        echo "Download qqwry database"
        wget ${SRC_FILE} -O ${FILE_NAME}
    else
        echo ${FILE_NAME} 'exist'
    fi

    if [[ ! -f setup.exe ]]; then
        unzip ${FILE_NAME}
    # else
        # echo "qqwry database ${FILE_NAME} download fail"
    fi

    if [[ ! -f ${QQWRY_PATH}/setup.exe ]]; then
        innoextract setup.exe
    elif [[ ! -f setup.exe ]]; then
        echo 'qqwry database setup.exe not exist'
    fi

    if [[ -f ${QQWRY_PATH}/app/qqwry.dat ]]; then
        mv app/qqwry.dat qqwry-${TIME}.dat
        # mv ${QQWRY_PATH}/app/qqwry.dat qqwry-${TIME}.dat
        echo "upload ${QQWRY_FILE}"
        cd ${IPPATH}
        upload qqwry/${QQWRY_FILE}
        # curl -T ${QQWRY_FILE} -u oss@live.hk:Xiaoke960318. "https://cloudflying-generic.pkg.coding.net/ip-data/ip-data/${QQWRY_FILE}?version=latest"
    elif [[ -f ${QQWRY_PATH}/${QQWRY_FILE} ]]; then
        echo "upload ${QQWRY_FILE}"
        cd ${IPPATH}
        upload qqwry/${QQWRY_FILE}
        # curl -T ${QQWRY_FILE} -u oss@live.hk:Xiaoke960318. "https://cloudflying-generic.pkg.coding.net/ip-data/ip-data/${QQWRY_FILE}?version=latest"
    fi

    if [[ -d ${QQWRY_PATH} ]]; then
        rm -fr ${QQWRY_PATH}
        echo 'Clean Env Completed'
    fi
}

# ip2region Start
if [ -z $(command -v java) ]; then
    apt update -y && apt install -y openjdk-8-jre
fi

function ip2region()
{
    IP2REGION_VERSION=$(curl -s https://raw.githubusercontent.com/lionsoul2014/ip2region/master/maker/java/build.xml | grep 'name="version"' | awk -F '"' '{print $4}')
    echo ${IP2REGION_VERSION}

    mkdir -p ${IPPATH}/ip2region/data
    cd ${IPPATH}/ip2region
    # curl -T ip2region-$(date +%Y%m%d).db -u oss@live.hk:Xiaoke960318. "https://cloudflying-generic.pkg.coding.net/ip-data/ip-data/ip2region-$(date +%Y%m%d).db?version=latest"
    # exit
    # if [[ ! -d $(pwd)/ip2region ]];then
    #     git clone --depth=1 ${GIT}        
    # fi

    # cd ip2region && git pull --force
    echo "=> Update IP DB FIle"
    curl -sSL ${IP2REGION_REGION} > ${IPPATH}/ip2region/data/global_region.csv
    curl -sSL ${IP2REGION_IPFILE} > ${IPPATH}/ip2region/data/ip.merge.txt
    echo "=> DB FIle Update Complete"

    if [[ ! -f ${IPPATH}/ip2region/dbMaker-1.2.2.jar ]]; then
        # curl -sSL ${IP2REGION_APP_URL} > ${IPPATH}/ip2region/dbMaker.jar
        wget ${IP2REGION_APP_URL} -O ${IPPATH}/ip2region/dbMaker.jar
    fi

    echo "=> Start Generate ip2region.db"
    java -jar dbMaker.jar -src ./data/ip.merge.txt -region ./data/global_region.csv
    mv -f data/ip2region.db ${IPPATH}/ip2region/ip2region-$(date +%Y%m%d).db
    echo "All done"
}

ip2location()
{
    # DB11LITE [DB11.LITE] IP-COUNTRY-REGION-CITY-LATITUDE-LONGITUDE-ZIPCODE-TIMEZONE Database
    # DBASNLITE [DBASN.LITE] IP-ASN Database 
    # DBASNLITEIPV6 [DBASN.LITE] IPV6-ASN Database
    # DB11LITEIPV6 [DB11.LITE] IPV6-COUNTRY-REGION-CITY-LATITUDE-LONGITUDE-ZIPCODE-TIMEZONE Database
    # DB-COUNTRY IP2Location™ Country Information
    # PX10LITE [PX10.LITE] IP2Proxy LITE IP-PROXYTYPE-COUNTRY-REGION-CITY-ISP-DOMAIN-USAGETYPE-ASN-LASTSEEN-THREAT-RESIDENTIAL 
    # PX10LITEIPV6 [PX10.LITE] IP2Proxy LITE IP-PROXYTYPE-COUNTRY-REGION-CITY-ISP-DOMAIN-USAGETYPE-ASN-LASTSEEN-THREAT-RESIDENTIAL 
    # CON-MUL IP2Location™ Continent Multilingual Database
    # DB-MUL IP2Location™ Country Multilingual Database
    # FLAG-ALL IP2Location™ Country Flags
    # WORD Maps https://www.ip2location.com/downloads/world-svg-map.zip
    # IP2Location™ GeoName ID https://www.ip2location.com/downloads/ip2location-geonameid.zip
    # https://www.ip2location.com/downloads/ip2location-continent-multilingual.zip
    CODES=(DB11LITE DBASNLITE DB11LITEIPV6 DBASNLITEIPV6 PX10LITE PX10LITEIPV6)
    TOKEN='cJ3MoOcXQYZY8qnuPTyuWqWwVhriUSZ7C2T0R5YH4YGco3HeXgITGEpCWRURKTYm'
    URL="https://www.ip2location.com/download/?token=${TOKEN}&file="
    SAVETO=${IPPATH}/ip2location
    mkdir -p ${SAVETO}
    # echo $URL
    for CODE in ${CODES[*]};do
        if [[ ! -f ${SAVETO}/${CODE}-${TIME}.zip ]]; then
            URLS=${URL}${CODE}
            wget ${URLS} -O ${SAVETO}/${CODE}-${TIME}.zip
            cd ${IPPATH}
            upload ip2location/${CODE}-${TIME}.zip
        fi
    done
}

# ip2region End
# bash ip2region.sh

upload()
{
    FILE=$1
    VERSION=$2
    if [[ -z ${VERSION} ]]; then
        VERSION='latest'
    fi

    CMD="curl -T ${FILE} -u oss@live.hk:Xiaoke960318. "https://cloudflying-generic.pkg.coding.net/storage/mirrors/${FILE}?version=${VERSION}""
    $CMD
}

delete()
{
    FILE=$1
    VERSION=$2
    if [[ -z ${VERSION} ]]; then
        VERSION='latest'
    fi

    CMD="curl -X DELETE -u oss@live.hk:Xiaoke960318. "https://cloudflying-generic.pkg.coding.net/storage/mirrors/${FILE}?version=${VERSION}""
    $CMD
}

iprange()
{
    echo 'Query arin apniic repe lacnic afrinic ip database and asn database'
    curl -sSL ${ARIN} > ${IPPATH}/arin-${TIME}.txt
    curl -sSL ${APNIC} > ${IPPATH}/apnic-${TIME}.txt 
    curl -sSL ${RIPE} > ${IPPATH}/ripe-${TIME}.txt
    curl -sSL ${LACNIC} > ${IPPATH}/lacnic-${TIME}.txt
    curl -sSL ${AFRINIC} > ${IPPATH}/afrinic-${TIME}.txt
    curl -sSL ${ASN} > ${IPPATH}/asn-${TIME}.txt
    cd ${IPPATH}
    curl -T arin-${TIME}.txt -u oss@live.hk:Xiaoke960318. "https://cloudflying-generic.pkg.coding.net/ip-data/ip-data/arin-${TIME}.txt?version=latest"
    curl -T apnic-${TIME}.txt -u oss@live.hk:Xiaoke960318. "https://cloudflying-generic.pkg.coding.net/ip-data/ip-data/apnic-${TIME}.txt?version=latest"
    curl -T ripe-${TIME}.txt -u oss@live.hk:Xiaoke960318. "https://cloudflying-generic.pkg.coding.net/ip-data/ip-data/ripe-${TIME}.txt?version=latest"
    curl -T lacnic-${TIME}.txt -u oss@live.hk:Xiaoke960318. "https://cloudflying-generic.pkg.coding.net/ip-data/ip-data/lacnic-${TIME}.txt?version=latest"
    curl -T afrinic-${TIME}.txt -u oss@live.hk:Xiaoke960318. "https://cloudflying-generic.pkg.coding.net/ip-data/ip-data/afrinic-${TIME}.txt?version=latest"
    curl -T asn-${TIME}.txt -u oss@live.hk:Xiaoke960318. "https://cloudflying-generic.pkg.coding.net/ip-data/ip-data/asn-${TIME}.txt?version=latest"
    
}

convert_asnames()
{
    FILE=`pwd`/databases/asnames.txt
    cat ${FILE} | grep 'No\ Registry\ Entry' > `pwd`/databases/asnames-no-registry.txt
    cat ${FILE} | grep -v 'No\ Registry\ Entry' > `pwd`/databases/asnames-normal.txt
    cat `pwd`/databases/asnames-normal.txt | awk -F ' ' '{print $1","$2$3}' > `pwd`/databases/asnames-normal-format.txt
    cat `pwd`/databases/asnames-no-registry.txt | awk -F ' ' '{print $1",NO_REGISTRY,"}' >> `pwd`/databases/asnames-normal-format.txt
}

start_down()
{
    # convert_asnames
    ip2location
    maxmind
    qqwry
    # echo ''
    # ip2region
    # iprange
}

# start_down
# date +%Y%m%d%H%M%S

case $1 in
    upload )
        upload $2 $3
        ;;
    delete )
        delete $2 $3
        ;;
    update)
        start_down
        ;;
    qqwry)
        qqwry
        ;;
    ip2region)
        ip2region
        ;;
    maxmind)
        maxmind
        ;;
    * )
        echo "Usage:"
        ;;
esac


END_TIME=$(date +%s)
((i=${END_TIME}-${START_TIME}))
echo "Total Time : $i s"