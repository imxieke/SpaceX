#!/usr/bin/env bash
###
 # @Author: Cloudflying
 # @Date: 2023-05-28 00:49:38
 # @LastEditTime: 2023-10-23 23:41:22
 # @LastEditors: Cloudflying
 # @Description: 解压文件
### 

ROOT_PATH=$(dirname $(dirname $(realpath $0)))
STORAGE_PATH=${ROOT_PATH}/storage
DATABASE_PATH=${ROOT_PATH}/databases
SOURCE_PATH=${ROOT_PATH}/databases/source

mkdir -p "${SOURCE_PATH}"/{rir,ipinfo,dbip,geolite,ip2location,ip2region,chunzhen}

if [[ ! -f "${SOURCE_PATH}/ip2location/IP2PROXY-LITE-PX11.BIN" ]]; then
	# Binary
	tar -C ${SOURCE_PATH}/ip2location/ -xvf ${STORAGE_PATH}/DB11LITEBIN.tar.gz IP2LOCATION-LITE-DB11.BIN
	tar -C ${SOURCE_PATH}/ip2location/ -xvf ${STORAGE_PATH}/DB11LITEBINIPV6.tar.gz IP2LOCATION-LITE-DB11.IPV6.BIN
	tar -C ${SOURCE_PATH}/ip2location/ -xvf ${STORAGE_PATH}/PX11LITEBIN.tar.gz IP2PROXY-LITE-PX11.BIN

	# CSV
	tar -C ${SOURCE_PATH}/ip2location/ -xvf ${STORAGE_PATH}/DB11LITECSV.tar.gz IP2LOCATION-LITE-DB11.CSV
	tar -C ${SOURCE_PATH}/ip2location/ -xvf ${STORAGE_PATH}/DB11LITECSVIPV6.tar.gz IP2LOCATION-LITE-DB11.IPV6.CSV
	# PX11 Lite
	tar -C ${SOURCE_PATH}/ip2location/ -xvf ${STORAGE_PATH}/PX11LITECSV.tar.gz IP2PROXY-LITE-PX11.CSV
	tar -C ${SOURCE_PATH}/ip2location/ -xvf ${STORAGE_PATH}/PX11LITECSVIPV6.tar.gz IP2PROXY-LITE-PX11.IPV6.CSV
	# ASN
	tar -C ${SOURCE_PATH}/ip2location/ -xvf ${STORAGE_PATH}/DBASNLITE.tar.gz IP2LOCATION-LITE-ASN.CSV
	tar -C ${SOURCE_PATH}/ip2location/ -xvf ${STORAGE_PATH}/DBASNLITEIPV6.tar.gz IP2LOCATION-LITE-ASN.IPV6.CSV
	tar -C ${SOURCE_PATH}/ip2location/ -xvf ${STORAGE_PATH}/CON-MUL.tar.gz IP2LOCATION-CONTINENT-MULTILINGUAL/IP2LOCATION-CONTINENT-MULTILINGUAL.CSV
	tar -C ${SOURCE_PATH}/ip2location/ -xvf ${STORAGE_PATH}/DB-COUNTRY.tar.gz IP2LOCATION-COUNTRY-INFORMATION/IP2LOCATION-COUNTRY-INFORMATION.CSV
	tar -C ${SOURCE_PATH}/ip2location/ -xvf ${STORAGE_PATH}/DB-MUL.tar.gz IP2LOCATION-COUNTRY-MULTILINGUAL/IP2LOCATION-COUNTRY-MULTILINGUAL.CSV
	tar -C ${SOURCE_PATH}/ip2location/ -xvf ${STORAGE_PATH}/DB-MUL.tar.gz IP2LOCATION-COUNTRY-MULTILINGUAL/IP2LOCATION-COUNTRY-MULTILINGUAL.CSV
	mv ${SOURCE_PATH}/ip2location/*/*.CSV ${SOURCE_PATH}/ip2location/
	rm -fr ${SOURCE_PATH}/ip2location/IP2LOCATION-CONTINENT-MULTILINGUAL 
	rm -fr ${SOURCE_PATH}/ip2location/IP2LOCATION-COUNTRY-INFORMATION
	rm -fr ${SOURCE_PATH}/ip2location/IP2LOCATION-COUNTRY-MULTILINGUAL
fi

# GeoLite2 MMDB
if [[ ! -f "${SOURCE_PATH}/geolite/GeoLite2-City.mmdb" ]]; then
	tar -C ${SOURCE_PATH}/geolite/ -xvf ${STORAGE_PATH}/GeoLite2-ASN.tar.gz "GeoLite2-ASN_*/GeoLite2-ASN.mmdb"
	tar -C ${SOURCE_PATH}/geolite/ -xvf ${STORAGE_PATH}/GeoLite2-City.tar.gz "GeoLite2-City_*/GeoLite2-City.mmdb"
	tar -C ${SOURCE_PATH}/geolite/ -xvf ${STORAGE_PATH}/GeoLite2-Country.tar.gz "GeoLite2-Country_*/GeoLite2-Country.mmdb"
	mv ${SOURCE_PATH}/geolite/*/*.mmdb ${SOURCE_PATH}/geolite/
	rm -fr ${SOURCE_PATH}/geolite/GeoLite2-*_*
fi

# GeoLite2 CSV
if [[ ! -f "${SOURCE_PATH}/geolite/GeoLite2-ASN-Blocks-IPv6.csv" ]]; then
	unzip ${STORAGE_PATH}/GeoLite2-ASN-CSV.zip -d ${SOURCE_PATH}/geolite/
	unzip ${STORAGE_PATH}/GeoLite2-City-CSV.zip -d ${SOURCE_PATH}/geolite/
	unzip ${STORAGE_PATH}/GeoLite2-Country-CSV.zip -d ${SOURCE_PATH}/geolite/
	mv ${SOURCE_PATH}/geolite/*/*.csv ${SOURCE_PATH}/geolite/
	rm -fr ${SOURCE_PATH}/geolite/GeoLite2-*-*_*
fi

if [[ ! -f "${SOURCE_PATH}/ipinfo/ipinfo-asn.mmdb" ]]; then
	cp -fr ${STORAGE_PATH}/ipinfo-asn.mmdb ${SOURCE_PATH}/ipinfo/
	cp -fr ${STORAGE_PATH}/ipinfo-country.mmdb ${SOURCE_PATH}/ipinfo/
	cp -fr ${STORAGE_PATH}/ipinfo-country_asn.mmdb ${SOURCE_PATH}/ipinfo/
	gzip -dkf ${STORAGE_PATH}/ipinfo-asn.csv.gz
	gzip -dkf ${STORAGE_PATH}/ipinfo-country.csv
	gzip -dkf ${STORAGE_PATH}/ipinfo-country_asn.csv
	mv ${STORAGE_PATH}/ipinfo-*.csv ${SOURCE_PATH}/ipinfo/
fi

if [[ ! -f "${SOURCE_PATH}/dbip/dbip-city.mmdb" ]]; then
	gzip -dkf ${STORAGE_PATH}/dbip-asn.mmdb.gz
	gzip -dkf ${STORAGE_PATH}/dbip-city.mmdb.gz
	gzip -dkf ${STORAGE_PATH}/dbip-country.mmdb.gz

	gzip -dkf ${STORAGE_PATH}/dbip-asn.csv.gz
	gzip -dkf ${STORAGE_PATH}/dbip-city.csv.gz
	gzip -dkf ${STORAGE_PATH}/dbip-country.csv.gz

	mv -f ${STORAGE_PATH}/dbip-*.mmdb ${SOURCE_PATH}/dbip/
	mv -f ${STORAGE_PATH}/dbip-*.csv ${SOURCE_PATH}/dbip/
fi

# if [[ ! -f "${SOURCE_PATH}/chunzhen/chunzhen.dat" ]]; then
# 	cp -fr ${STORAGE_PATH}/chunzhen.dat ${SOURCE_PATH}/chunzhen/chunzhen.dat
# fi

# if [[ ! -f "${SOURCE_PATH}/rir/rir-asn.txt" ]]; then
# 	cp -fr ${STORAGE_PATH}/rir-asn.txt ${SOURCE_PATH}/rir/rir-asn.txt
# fi

# if [[ ! -f "${SOURCE_PATH}/rir/rir-v4.txt" ]]; then
# 	cp -fr ${STORAGE_PATH}/rir-v4.txt ${SOURCE_PATH}/rir/rir-v4.txt
# fi

# if [[ ! -f "${SOURCE_PATH}/rir/rir-v6.txt" ]]; then
# 	cp -fr ${STORAGE_PATH}/rir-v6.txt ${SOURCE_PATH}/rir/rir-v6.txt
# fi