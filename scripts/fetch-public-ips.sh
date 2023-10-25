#!/usr/bin/env bash
###
 # @Author: Cloudflying
 # @Date: 2023-05-08 22:02:37
 # @LastEditTime: 2023-05-08 23:34:18
 # @LastEditors: Cloudflying
 # @Description: Fetch ip data
### 

ROOT_DIR=$(dirname $(dirname $(realpath $0)))

fetch_ips()
{
    # Linode
    curl -sL https://geoip.linode.com --output $ROOT_DIR/databases/source/linode-ips.csv
    # Google
    curl -sL https://www.gstatic.com/ipranges/goog.json --output $ROOT_DIR/databases/source/google-ips.json
    # Google Cloud
    curl -sL https://www.gstatic.com/ipranges/cloud.json --output $ROOT_DIR/databases/source/google-cloud-ips.json
    # Cloudflare
    curl -sL https://www.cloudflare.com/ips-v4 --output $ROOT_DIR/databases/source/cloudflare-ips-v4.json
    curl -sL https://www.cloudflare.com/ips-v6 --output $ROOT_DIR/databases/source/cloudflare-ips-v6.json
    ## Github
    curl -sL https://api.github.com/meta --output $ROOT_DIR/databases/source/github-ips.json

    # Amazon IP Range
    # https://docs.aws.amazon.com/general/latest/gr/aws-ip-ranges.html
	# https://aws.amazon.com/cn/blogs/aws/aws-ip-ranges-json/
    # http://d7uri8nf7uskq.cloudfront.net/tools/list-cloudfront-ips include below
    curl -sL https://ip-ranges.amazonaws.com/ip-ranges.json --output $ROOT_DIR/databases/source/amazon_ips.json

    # Fastly
    curl -sL https://api.fastly.com/public-ip-list --output $ROOT_DIR/databases/source/fastly-ips.json
    # MaxCDN Web Use Cloudflare CDN
    # https://support.maxcdn.com/hc/en-us/articles/360036932271-IP-Blocks
    curl -sL https://support.maxcdn.com/hc/en-us/article_attachments/360051920551/maxcdn_ips.txt --output $ROOT_DIR/databases/source/maxcdn_ips.txt
    curl -sL https://core.telegram.org/resources/cidr.txt --output $ROOT_DIR/databases/source/telegram_ips.txt
    # Oracle
    # Location Details https://docs.oracle.com/en-us/iaas/Content/General/Concepts/addressranges.htm
    curl -sL https://docs.oracle.com/en-us/iaas/tools/public_ip_ranges.json --output $ROOT_DIR/databases/source/oracle_ips.json

    # Redhat
    curl -sL https://access.redhat.com/articles/1525183 | grep -Eo "^((\d|[1-9]\d|1\d\d|2([0-4]\d|5[0-5]))\.){3}((\d|[1-9]\d|1\d\d|2([0-4]\d|5[0-5]))\/){1}(\d|[1-3]\d)$"
    # https://su.baidu.com/help/index.html#/10_changjianwenti/0_HIDE_FAQ/20_baiduyunjiasujiedianIPdizhiduan.md
    curl -sL https://apidoc.su.baidu.com/newdoc/entryyunjiasu.js | grep -Eo '((2(5[0-5]|[0-4]\d))|[0-1]?\d{1,2})(\.((2(5[0-5]|[0-4]\d))|[0-1]?\d{1,2})){3}\/(2[0-9]|[0-9])' | grep -Ev '100.100|192.168.0|1.2.3'
    # IBM
    curl -sL https://github.com/ibm-cloud-docs/cloud-infrastructure/raw/master/ips.md | grep -Eo '((2(5[0-5]|[0-4]\d))|[0-1]?\d{1,2})(\.((2(5[0-5]|[0-4]\d))|[0-1]?\d{1,2})){3}\/(2[0-9]|[0-9])' | grep -Ev '^10.' | sort -h
}

fetch_microsoft_ip_range()
{
    # azure public cloud
    curl -sL https://www.microsoft.com/en-in/download/confirmation.aspx?id=56519 | grep -Eo 'http\S+://download.microsoft.com/download/\S+.json' | head -n 1
    # azure china cloud
    curl -sL https://www.microsoft.com/en-in/download/confirmation.aspx?id=57062 | grep -Eo 'http\S+://download.microsoft.com/download/\S+.json' | head -n 1
    # azure Government cloud
    curl -sL https://www.microsoft.com/en-in/download/confirmation.aspx?id=57063 | grep -Eo 'http\S+://download.microsoft.com/download/\S+.json' | head -n 1
    # azure Germany cloud
    curl -sL https://www.microsoft.com/en-in/download/confirmation.aspx?id=57064 | grep -Eo 'http\S+://download.microsoft.com/download/\S+.json' | head -n 1
    # Microsoft IP Range GeoLocation 两个文件稍后处理 
    curl -sL https://www.microsoft.com/en-in/download/confirmation.aspx?id=53601 | grep -Eo 'http\S+://download.microsoft.com/download/\S+.csv' | head -n -1 | tail -n +2 | sort | uniq
    # Microsoft Public IP Space
    curl -sL https://www.microsoft.com/en-in/download/confirmation.aspx?id=53602 | grep -Eo 'http\S+://download.microsoft.com/download/\S+.csv' | head -n 1

    # Office 365 URLs and IP address ranges jsonformat
    curl -sL https://learn.microsoft.com/en-us/microsoft-365/enterprise/urls-and-ip-address-ranges?view=o365-worldwide | grep -Eo 'https://endpoints.office.com/endpoints\S+"' | sed 's#"##g'
	# Office 365 operated by 21 Vianet
    curl -sL https://learn.microsoft.com/en-us/microsoft-365/enterprise/urls-and-ip-address-ranges-21vianet?view=o365-worldwide | grep -Eo 'https://endpoints.office.com/endpoints\S+"' | sed 's#"##g'
	# Office 365 U.S. Government DoD
    curl -sL https://learn.microsoft.com/en-us/microsoft-365/enterprise/microsoft-365-u-s-government-dod-endpoints?view=o365-worldwide | grep -Eo 'https://endpoints.office.com/endpoints\S+"' | sed 's#"##g'
	# Office 365 U.S. Government GCC High
    curl -sL https://learn.microsoft.com/en-us/microsoft-365/enterprise/microsoft-365-u-s-government-gcc-high-endpoints?view=o365-worldwide | grep -Eo 'https://endpoints.office.com/endpoints\S+"' | sed 's#"##g'
}

fetch_ips