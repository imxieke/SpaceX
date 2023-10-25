## 区域互联网注册机构（Regional Internet Registry，RIR）

- 5 RIP (afrinic, apnic, arin, lacnic, ripe ncc)

## IPV6 数据源
- https://bgp.potaroo.net/stats/allocspace-prefix6.txt
- https://bgp.potaroo.net/stats/freespace-prefix6.txt

https://ftp.apnic.net/apnic/whois/apnic.db.inetnum.gz

- http://ftp.arin.net
- http://ftp.afrinic.net
- http://ftp.apnic.net
- http://ftp.lacnic.net
- https://ftp.ripe.net

### Whois DB
- https://bgp.potaroo.net/stats/lacnic/bulkwhois.txt
- https://bgp.potaroo.net/stats/iana/iana-legacy.xml
- https://bgp.potaroo.net/stats/apnic/reserved_advertised_ipv4.txt
- https://bgp.potaroo.net/stats/apnic/reserved_unadvertised_ipv4.txt
- https://bgp.potaroo.net/stats/cc_allocations/cckey.csv
- https://bgp.potaroo.net/stats/iana/archive/2022/06/ipv6-unicast-address-assignments-20220618.xml
- https://bgp.potaroo.net/stats/iana/archive/2022/06/as-numbers-20220618.xml
- https://bgp.potaroo.net/stats/iana/archive/2022/06/ipv4-address-space-20220618.xml
- https://bgp.potaroo.net/stats/iana/archive/2022/06/ipv6-address-space-20220618.xml

ipv4 ipv6 asn
- https://bgp.potaroo.net/stats/afrinic/afrinic.db.aut-num 疑似 Whois 数据库
- https://bgp.potaroo.net/stats/afrinic/afrinic.db.inet6num
- https://bgp.potaroo.net/stats/afrinic/afrinic.db.inetnum

- https://bgp.potaroo.net/stats/ripe/ripe.db.aut-num
- https://bgp.potaroo.net/stats/ripe/ripe.db.inet6num
- https://bgp.potaroo.net/stats/ripe/ripe.db.inetnum

- https://bgp.potaroo.net/stats/ripencc/ripe.db.inetnum
- https://bgp.potaroo.net/stats/ripencc/ripe.db.aut-num
- https://bgp.potaroo.net/stats/ripencc/ripe.db.inet6num

- https://bgp.potaroo.net/stats/apnic/jpnic.db.txt
- https://ftp.ripe.net/ripe/dbase/ripe-nonauth.db.gz
- https://bgp.potaroo.net/stats/apnic/apnic.db.txt
- https://ftp.ripe.net/ripe/dbase/ripe.db.gz
- http://ftp.afrinic.net/dbase/afrinic.db.gz
- http://ftp.apnic.net/apnic/dbase/data/jpnic.db.gz
- http://ftp.apnic.net/apnic/dbase/data/krnic.db.gz
- http://ftp.apnic.net/apnic/dbase/data/twnic.db.gz
- https://bgp.potaroo.net/stats/arin/arin.db.txt
- https://bgp.potaroo.net/stats/arin/zip/arin_db.txt
- http://ftp.lacnic.net/lacnic/dbase/lacnic.db.csv.gz
- http://ftp.lacnic.net/lacnic/dbase/lacnic.db.gz
- http://ftp.lacnic.net/lacnic/irr/lacnic.db.gz

## IPV4 数据源
- https://bgp.potaroo.net/stats/allocspace-prefix.txt
- https://bgp.potaroo.net/stats/freespace-prefix.txt


http://www.apnic.net/db/rir-stats-format.html

ARIN	RIPE NCC	APNIC	LACNIC	AFRINIC
afrinic,apnic,arin,iana,lacnic,ripencc
RIR File Format
- registry|cc|type|start|value|date|status[|extensions...]
- cc CountryCode
- Type ASN IPV4 IPV6
- start ip start
- value ip 分配数量 ASN 类型则为 1  ipv4 为数量 ipv6 为 CIDR
- date 分配日期
- status 分配状态