#!/usr/bin/env bash
###
 # @Author: Cloudflying
 # @Date: 2023-05-28 15:26:30
 # @LastEditTime: 2023-10-23 23:55:53
 # @LastEditors: Cloudflying
 # @Description: 
### 

ROOT_PATH=$(dirname $(dirname $(realpath $0)))
STORAGE_PATH=${ROOT_PATH}/storage
DATABASE_PATH=${ROOT_PATH}/databases
SOURCE_PATH=${ROOT_PATH}/databases/source

if [[ ! -f '/tmp/rir/txt' ]]; then
	cat "${STORAGE_PATH}"/rir/* > /tmp/rir.txt
fi

if [[ ! -f '/tmp/rir-asn.txt' ]]; then
	grep -v '#' /tmp/rir.txt \
		| grep -v 'summary' \
		| grep 'asn' \
		| awk -F "|" '{print $4","$5","$2","$7","$6","$1}' \
		| sort --human-numeric-sort | uniq > /tmp/rir-asn.txt
fi

if [[ ! -f '/tmp/rir-ipv4.txt' ]]; then
	grep -v '#' /tmp/rir.txt \
		| grep -v 'summary' \
		| grep 'ipv4' \
		| awk -F "|" '{print $4","$5","$2","$7","$6","$1}' \
		| sort --human-numeric-sort | uniq > /tmp/rir-ipv4.txt
fi

if [[ ! -f '/tmp/rir-ipv6.txt' ]]; then
	grep -v '#' /tmp/rir.txt \
		| grep -v 'summary' \
		| grep 'ipv6' \
		| awk -F "|" '{print $4","$5","$2","$7","$6","$1}' \
		| sort --human-numeric-sort | uniq > /tmp/rir-ipv6.txt
fi


exit 0 #success





rir_lists=(
	"delegated-afrinic-extended-latest"
	"delegated-apnic-extended-latest"
	"delegated-arin-extended-latest"
	"delegated-lacnic-extended-latest"
	"delegated-ripencc-extended-latest"
	"delegated-afrinic-latest"
	"delegated-apnic-latest"
	"delegated-iana-latest"
	"delegated-lacnic-latest"
	"delegated-ripencc-latest"
	"legacy-apnic-latest"
)

cd ${STORAGE_PATH}

if [[ ! -f "${STORAGE_PATH}/rir.txt" ]]; then
	# cat ${rir_lists[*]} > ${STORAGE_PATH}/rir.txt
	cat ${STORAGE_PATH}/rir/* > ${STORAGE_PATH}/rir.txt
fi

if [[ ! -f "${STORAGE_PATH}/rir-asn.txt" ]]; then
	grep -v '#' ${STORAGE_PATH}/rir.txt \
		| grep -v 'summary' \
		| grep 'asn' \
		| awk -F "|" '{print $4","$5","$2","$7","$6","$1}' \
		| sort --human-numeric-sort | uniq > ${STORAGE_PATH}/rir-asn.txt
fi

if [[ ! -f "${STORAGE_PATH}/rir-v4.txt" ]]; then
	grep -v '#' ${STORAGE_PATH}/rir.txt \
		| grep -v 'summary' \
		| grep 'ipv4' \
		| awk -F "|" '{print $4","$5","$2","$7","$6","$1}' \
		| sort --human-numeric-sort | uniq > ${STORAGE_PATH}/rir-v4.txt
fi

if [[ ! -f "${STORAGE_PATH}/rir-v6.txt" ]]; then
	grep -v '#' ${STORAGE_PATH}/rir.txt \
		| grep -v 'summary' \
		| grep 'ipv6' \
		| awk -F "|" '{print $4","$5","$2","$7","$6","$1}' \
		| sort --human-numeric-sort | uniq > ${STORAGE_PATH}/rir-v6.txt
fi

cd ${ROOT_PATH}