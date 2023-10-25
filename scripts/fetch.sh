#!/usr/bin/env bash
###
 # @Author: Cloudflying
 # @Date: 2023-10-23 23:04:25
 # @LastEditTime: 2023-10-24 00:44:24
 # @LastEditors: Cloudflying
 # @Description: Fetch Database
### 

MIRRORS_URL="https://mirrors.xie.ke/ipdb"

ROOT_PATH=$(dirname $(dirname $(realpath $0)))
STORAGE_PATH=${ROOT_PATH}/storage
# ip2region will be create by git clone
mkdir -p "${STORAGE_PATH}"/{rir,ipinfo,dbip,geolite,ip2location,ip2region,chunzhen}

_fetch()
{
	URL=$1
	SAVE=$2
	if [[ ! -f "$SAVE" ]]; then
		wget -c --header="User-Agent: Mozilla/5.0" --no-check-certificate "${URL}" --output-document "${SAVE}"
	else
		echo $(date "+%Y-%m-%e %k:%M:%S") ": ${SAVE} already exists"
	fi
}

geolite_dbs=(
	"GeoLite2-ASN"
	"GeoLite2-City"
	"GeoLite2-Country"
)

geolite_dbs_csv=(
	"GeoLite2-ASN-CSV"
	"GeoLite2-City-CSV"
	"GeoLite2-Country-CSV"
)

_fetch "${MIRRORS_URL}"/ip2region/ip2region-latest.xdb "${STORAGE_PATH}"/ip2region/ip2region.xdb
_fetch "${MIRRORS_URL}"/chunzhen/chunzhen-latest.dat "${STORAGE_PATH}"/chunzhen/chunzhen.dat

# Monthly
IP2LOCATION_DBS=(
	CON-MUL
	DB-MUL
	FLAG-ALL
	DB-COUNTRY
	DB11LITECSV
	DB11LITEBIN
	DB11LITECSVIPV6
	DB11LITEBINIPV6
	PX11LITECSV
	PX11LITEBIN
	PX11LITECSVIPV6
	DBASNLITE
	DBASNLITEIPV6
)

for ipdb in ${IP2LOCATION_DBS[*]}
do
	IP2LOCATION_FULL_URL="${MIRRORS_URL}/ip2location/${ipdb}"
	_fetch ${IP2LOCATION_FULL_URL}-latest.tar.gz ${STORAGE_PATH}/ip2location/${ipdb}.tar.gz
done

# db-ip.com Monthly Update
# https://db-ip.com/db/lite.php
DB_DATE=$(date "+%Y-%m")
_fetch ${MIRRORS_URL}/dbip/dbip-country-latest.csv.gz "${STORAGE_PATH}/dbip/dbip-country.csv.gz"
_fetch ${MIRRORS_URL}/dbip/dbip-country-latest.mmdb.gz "${STORAGE_PATH}/dbip/dbip-country.mmdb.gz"
_fetch ${MIRRORS_URL}/dbip/dbip-city-latest.csv.gz "${STORAGE_PATH}/dbip/dbip-city.csv.gz"
_fetch ${MIRRORS_URL}/dbip/dbip-city-latest.mmdb.gz "${STORAGE_PATH}/dbip/dbip-city.mmdb.gz"
_fetch ${MIRRORS_URL}/dbip/dbip-asn-latest.csv.gz "${STORAGE_PATH}/dbip/dbip-asn.csv.gz"
_fetch ${MIRRORS_URL}/dbip/dbip-asn-latest.mmdb.gz "${STORAGE_PATH}/dbip/dbip-asn.mmdb.gz"

# ipinfo.io daily update
ipinfo_dbs=(
	asn.mmdb
	country.mmdb
	country_asn.mmdb
	asn.csv.gz
	country.csv.gz
	country_asn.csv.gz
)

for ipinfo_db in ${ipinfo_dbs[*]}
do
	_fetch "${MIRRORS_URL}/ipinfo/ipinfo-latest-${ipinfo_db}" ${STORAGE_PATH}/ipinfo/${ipinfo_db}
done

# 一周一次
geolite_dbs=(
	"GeoLite2-ASN"
	"GeoLite2-City"
	"GeoLite2-Country"
)

geolite_dbs_csv=(
	"GeoLite2-ASN-CSV"
	"GeoLite2-City-CSV"
	"GeoLite2-Country-CSV"
)

GEOLITE_URL="${MIRRORS_URL}/geolite"

for geodb in "${geolite_dbs[@]}"
do
	_fetch "${GEOLITE_URL}/${geodb}-latest.tar.gz" "${STORAGE_PATH}/geolite/${geodb}.tar.gz"
done

for geodb_csv in "${geolite_dbs_csv[@]}"
do
	_fetch "${GEOLITE_URL}/${geodb_csv}-latest.zip" "${STORAGE_PATH}/geolite/${geodb_csv}.zip"
done

# ASN
# _fetch http://ftp.arin.net/info/asn.txt ${STORAGE_PATH}/arin-asn.txt
_fetch https://ftp.ripe.net/ripe/asnames/asn.txt ${STORAGE_PATH}/ripe-asn.txt
_fetch https://ftp.ripe.net/iso3166-countrycodes.txt ${STORAGE_PATH}/ripe-iso3166-countrycodes.txt

# BGP_RIR_URL="https://bgp.potaroo.net/stats"

# Ripe
# _fetch ${BGP_RIR_URL}/ripe/delegated ${STORAGE_PATH}/rir/ripe-delegated
# _fetch ${BGP_RIR_URL}/ripe/delegated-extended ${STORAGE_PATH}/rir/ripe-delegated-extended

# Afrinic
# _fetch ${BGP_RIR_URL}/afrinic/delegated ${STORAGE_PATH}/rir/afrinic-delegated
# _fetch ${BGP_RIR_URL}/afrinic/delegated-extended ${STORAGE_PATH}/rir/afrinic-delegated-extended

# Apnic
# _fetch ${BGP_RIR_URL}/apnic/delegated ${STORAGE_PATH}/rir/apnic-delegated
# _fetch ${BGP_RIR_URL}/apnic/delegated-extended ${STORAGE_PATH}/rir/apnic-delegated-extended
# _fetch ${BGP_RIR_URL}/apnic/delegated.orig ${STORAGE_PATH}/rir/apnic-delegated-orig

# Arin
# _fetch ${BGP_RIR_URL}/arin/delegated ${STORAGE_PATH}/rir/arin-delegated
# _fetch ${BGP_RIR_URL}/arin/delegated-extended ${STORAGE_PATH}/rir/arin-delegated-extended

# lacnic
# _fetch ${BGP_RIR_URL}/lacnic/delegated ${STORAGE_PATH}/rir/lacnic-delegated
# _fetch ${BGP_RIR_URL}/lacnic/delegated-extended ${STORAGE_PATH}/rir/lacnic-delegated-extended

# IANA 数据似乎过时的
# _fetch ${BGP_RIR_URL}/iana/delegated ${STORAGE_PATH}/rir/iana-delegated
# _fetch ${BGP_RIR_URL}/iana/delegated-extended ${STORAGE_PATH}/rir/iana-delegated-extended
# _fetch ${BGP_RIR_URL}/iana/delegated-iana ${STORAGE_PATH}/rir/iana-delegated-iana
# _fetch ${BGP_RIR_URL}/iana/delegated-iana-extended ${STORAGE_PATH}/rir/iana-delegated-iana-extended
# _fetch ${BGP_RIR_URL}/iana/delegated-iana-latest ${STORAGE_PATH}/rir/iana-delegated-iana-latest
# _fetch ${BGP_RIR_URL}/iana/delegated.txt ${STORAGE_PATH}/rir/iana-delegated-txt

# RIR More
# 数据重复
# _fetch ${BGP_RIR_URL}/nro/status.joint.txt ${STORAGE_PATH}/rir/nro-status-joint
# _fetch ${BGP_RIR_URL}/nro/delegated.nro.txt ${STORAGE_PATH}/rir/nro-delegated-nro
# _fetch ${BGP_RIR_URL}/nro/unallocated.nro.txt ${STORAGE_PATH}/rir/nro-unallocated-nro
# 文件重复 与上面文件 hash 一致
# _fetch ${BGP_RIR_URL}/nro/delegated.joint.txt ${STORAGE_PATH}/rir/nro-delegated-joint
# _fetch ${BGP_RIR_URL}/nro/unallocated.joint.txt ${STORAGE_PATH}/rir/nro-unallocated-joint

# 5 RIR
# http://ftp.afrinic.net/stats/afrinic/delegated-afrinic-latest
# http://ftp.afrinic.net/stats/afrinic/delegated-afrinic-extended-latest

# # Afrinic
# _fetch https://ftp.ripe.net/pub/stats/afrinic/delegated-afrinic-latest ${STORAGE_PATH}/delegated-afrinic-latest
# _fetch https://ftp.ripe.net/pub/stats/afrinic/delegated-afrinic-extended-latest ${STORAGE_PATH}/delegated-afrinic-extended-latest

# # Ripe
# _fetch https://ftp.ripe.net/pub/stats/ripencc/delegated-ripencc-extended-latest ${STORAGE_PATH}/delegated-ripencc-extended-latest
# _fetch https://ftp.ripe.net/pub/stats/ripencc/delegated-ripencc-latest ${STORAGE_PATH}/delegated-ripencc-latest

# # Lacnic
# _fetch https://ftp.ripe.net/pub/stats/lacnic/delegated-lacnic-latest ${STORAGE_PATH}/delegated-lacnic-latest
# _fetch https://ftp.ripe.net/pub/stats/lacnic/delegated-lacnic-extended-latest ${STORAGE_PATH}/delegated-lacnic-extended-latest

# # Arin
# _fetch https://ftp.ripe.net/pub/stats/arin/delegated-arin-extended-latest ${STORAGE_PATH}/delegated-arin-extended-latest

# Apnic
# # https://ftp.ripe.net/pub/stats/apnic/assigned-apnic-latest
# # https://ftp.ripe.net/pub/stats/apnic/delegated-apnic-ipv6-assigned-latest
# _fetch https://ftp.ripe.net/pub/stats/apnic/delegated-apnic-extended-latest ${STORAGE_PATH}/delegated-apnic-extended-latest
# _fetch https://ftp.ripe.net/pub/stats/apnic/delegated-apnic-latest ${STORAGE_PATH}/delegated-apnic-latest
# _fetch https://ftp.ripe.net/pub/stats/apnic/legacy-apnic-latest ${STORAGE_PATH}/legacy-apnic-latest

# _fetch http://ftp.apnic.net/stats/iana/delegated-iana-latest ${STORAGE_PATH}/delegated-iana-latest