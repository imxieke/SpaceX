#!/usr/bin/env bash
###
 # @Author: Cloudflying
 # @Date: 2022-06-20 17:33:15
 # @LastEditTime: 2023-05-08 23:34:15
 # @LastEditors: Cloudflying
 # @Description: 
 # @FilePath: /geoip/scripts/utils.sh
### 

ROOT_DIR=$(dirname $(dirname $(realpath $0)))

filter_rir()
{
    cd $ROOT_DIR/databases/source/rir
    [ -f /tmp/rir-all.txt ] || cat `ls | grep -Ei 'delegated|apnic-*-latest|nro-*|ripecc-extended' | grep -v 'transfer'` > /tmp/rir-all.txt
    [ -f /tmp/rir-asn.txt ] || grep 'asn' /tmp/rir-all.txt | grep -v '\|summary' > /tmp/rir-asn.txt
    [ -f /tmp/rir-ipv4.txt ] || grep 'ipv4' /tmp/rir-all.txt | grep -v '\|summary' > /tmp/rir-ipv4.txt
    [ -f /tmp/rir-ipv6.txt ] || grep 'ipv6' /tmp/rir-all.txt | grep -v '\|summary' > /tmp/rir-ipv6.txt
    [ -f $ROOT_DIR/databases/source/rir-asn.csv ] || awk -F '|' '{print $4","$2","$1","$6","$7}' /tmp/rir-asn.txt | sort -t ',' -k 1,1 --human-numeric-sort | uniq > $ROOT_DIR/databases/source/rir-asn.csv
    [ -f $ROOT_DIR/databases/source/rir-ipv4.csv ] || awk -F '|' '{print $4"/"$5","$2","$1","$6","$7}' /tmp/rir-ipv4.txt | sort -t ',' -k 1,1 --human-numeric-sort | uniq > $ROOT_DIR/databases/source/rir-ipv4.csv
    [ -f $ROOT_DIR/databases/source/rir-ipv6.csv ] || awk -F '|' '{print $4"/"$5","$2","$1","$6","$7}' /tmp/rir-ipv6.txt | sort -t ',' -k 1,1 --human-numeric-sort | uniq > $ROOT_DIR/databases/source/rir-ipv6.csv
    [ -f /tmp/rir-ips-v4.txt ] || awk -F ',' '{print $1}' /tmp/rir-ipv4-format.csv > /tmp/rir-ips-v4.txt # 非 CIDR 格式
    [ -f $ROOT_DIR/databases/source/rir-ips-v6.txt ] || awk -F ',' '{print $1}' /tmp/rir-ipv6-format.csv > $ROOT_DIR/databases/source/rir-ips-v6.txt
    echo "Done"
}

# 需要手动检查
# 创宇云/加速乐
# https://www.yunaq.com/help/article?id=5d085acd0fbdcd001a233901
# 奇安信
# https://wangzhan.qianxin.com/notice/detail/10057
# DNSPOD
# https://support.dnspod.cn/status/
# https://support.dnspod.cn/dp_api/status/list/
# D 监控
# https://docs.dnspod.cn/d-monitor/d-monitor-ip/

# filter_rir
fetch_microsoft_ip_range