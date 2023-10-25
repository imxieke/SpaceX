IP地址块222.125.80.128/26包含的可用主机数是多少，最小的地址是多少，最大的地址是多少？
IP/26 是CIDR的格式，全称是classless inter domain route 叫做无类域间路由，就是说32位IP的前26位为网络号，后面的全部都可以分给主机：
222.125.80.128------------1101 1110.0111 1101.0101 0000.10（00 0000）
1101 1110.0111 1101.0101 0000.10  总共26位为网络号
括号里面是主机位，总共六位。 2^6=64
再除掉222.125.80.128即主机位全零地址，和222.125.80.191即广播地址外
还剩64-2=62个主机地址。
其中最小地址主机位00 0001即222.125.80.129
最大地址主机位11 1110即222.125.80.190

问题：
计算网段 172.16.0.0/23 的IP地址段是多少到多少？

解答：
1、由题可得起始IP地址为：172.16.0.1
2、其中23为子网掩码用“位数”的简写方式，意思是子网掩码的二进制为从左到右23个1组成的二进制 11111111.11111111.11111110.00000000，转换为十进制结果为255.255.254.0，并得出右侧为0的有9位可以表示主机段
3、计算广播地址：按如下方法将IP地址段和子网掩码的二进制格式对齐进行计算，垂直都是1的得1否则得0，然后将右侧9位0全部设置为1，如下所示

10101100-00010000-00000000-00000000
11111111-11111111-11111110-00000000
-----------------------------------
10101100-00010000-00000001-11111111

4、将计算结果转换为十进制，得出广播地址为172.16.1.255
5、由此可以得出本题IP地址段的范围是 172.16.0.1 至 172.16.1.254
6、可用IP数量数速算为2的9次方减2=510


子网掩码是一个32位地址，用于屏蔽IP地址的一部分以区别网络标识和主机标识，并说明该IP地址是在局域网上，还是在远程网上。

子网掩码不能单独存在，它必须结合IP地址一起使用。子网掩码只有一个作用，就是将某个IP地址划分成网络地址和主机地址两部分。

子网掩码——屏蔽一个IP地址的网络部分的“全1”比特模式。对于A类地址来说，默认的子网掩码是255.0.0.0；对于B类地址来说默认的子网掩码是255.255.0.0；对于C类地址来说默认的子网掩码是255.255.255.0。

IP组播地址范围

所有的多播地址可以很容易被认出，因为同位模式“1110”开始。

224.0.0.0 - 224.0.0.255知名多播地址，控制通道

224.0.1.0 - 238.255.255.255全球范围的（互联网宽）组播地址

239.0.0.0 - 239.255.255.255本地多播地址


ip地址后边加个/8(16,24,32)是掩码的位数，

A类IP地址的默认子网掩码为255.0.0.0（由于255相当于二进制的8位1，所以也缩写成“/8”，表示网络号占了8位）; 即11111111.00000000.00000000.00000000
B类的为255.255.0.0（/16）; 即11111111.11111111.00000000.00000000
C类的为255.255.255.0(/24)；即11111111.11111111.11111111.00000000
/30就是255.255.255.252；即11111111.11111111.11111111.11111100
/32就是255.255.255.255；即11111111.11111111.11111111.11111111


子网掩码转换为IP范围
 
在网络应用中,经常需要将子网掩码转换为IP范围,以便进行进一步的计算.
以下是转换的原理及代码:    
一 子网掩码的作用, 就是将某个IP地址划分为'子网编号'和'主机地址'
   掩码格式       [子网编号:26bit               ]主机地址:6bit
   172.16.2.64/26 [10101100 00010000 00000010 01]000000
   172.16.2.96/26 [10101100 00010000 00000010 01]100000
   6bit的主机地址, 可以容纳63个编号. 则上面两个掩码的范围是
   掩码格式       IP范围
   172.16.2.64/26 [172.16.2.64 - 172.16.2.127]
   172.16.2.96/26 [172.16.2.96 - 172.16.2.127] *上一条的子集
 
二 算法    
typedef struct
{
u_int32_t min;
u_int32_t max;
}ip_range_t;
int str2iprange( ip_range_t *ipr, char *sipr)
{
char sip[16];
char smask[3];
sscanf( sipr, "%[^/]/%s", sip, smask);
ipr->min = ntohl(inet_addr( sip));
ipr->max = (ipr->min & ~( (0x1 << (32 - atoi(smask))) - 1)) + ( (0x1 << (32 - atoi(smask))) - 1);
}
三 测试    
ip_range_t ipr;
str2iprange( &ipr, "172.16.2.94/24");
struct in_addr min = { htonl( ipr.min)};
printf("%s - ", inet_ntoa( min));
struct in_addr max = { htonl( ipr.max)};
printf("%s /n", inet_ntoa( max));
输出结果:
172.16.2.94 - 172.16.2.127
END

为方便自己以后查询，故转自:http://www.th7.cn/system/lin/201301/36773.shtml

IP地址202.112.14.137的子网掩码为什么是255.255.255.224?
如果这个掩码为255.255.255.224是因为它划分了2的三次方即8个子网，减去网络地址和广播地址部分，只能有6个子网。
8位被掩码借去3位还剩下5位，即2的5次方为32，
同样也应该减去网络地址和广播地址，即为30个主机。
所以这样的掩码是分为6个子网。每个子网可有30台主机。

题型：一个主机的IP地址是202.112.14.137，掩码是255.255.255.224，要求计算这个主机所在网络的网络地址和广播地址。
常规办法是把这个主机地址和子网掩码都换算成二进制数，两者进行逻辑与运算后即可得到网络地址。其实大家只要仔细想想，可以得到另一个方法：255.255.255.224的掩码所容纳的IP地址有256－224＝32个（包括网络地址和广播地址），那么具有这种掩码的网络地址一定是32的倍数。而网络地址是子网IP地址的开始，广播地址是结束，可使用的主机地址在这个范围内，因此略小于137而又是32的倍数的只有128，所以得出网络地址是202.112.14.128。而广播地址就是下一个网络的网络地址减1。而下一个32的倍数是160，因此可以得到广播地址为202.112.14.159。可参照下表来理解本例。
子网络 2进制子网络域数 2进制主机域数的范围 2进制主机域数的范围
第1个子网络 000 00000 thru 11111 .0 thru.31
第2个子网络 001 00000 thru 11111 .32 thru.63
第3个子网络 010 00000 thru 11111 .64 thru.95
第4个子网络 011 00000 thru 11111 .96 thru.127
第5个子网络 100 00000 thru 11111 .128 thru.159
第6个子网络 101 00000 thru 11111 .160 thru.191
第7个子网络 110 00000 thru 11111 .192 thru.223
第8个子网络 111 00000 thru 11111 .124 thru.255

题型，要你根据每个网络的主机数量进行子网地址的规划和计算子网掩码。这也可按上述原则进行计算。比如一个子网有10台主机，那么对于这个子网需要的IP地址是：
10＋1＋1＋1＝13
注意：加的第一个1是指这个网络连接时所需的网关地址，接着的两个1分别是指网络地址和广播地址。因为13小于16（16等于2的4次方），所以主机位为4位。而
256－16＝240
所以该子网掩码为255.255.255.240。
如果一个子网有14台主机，不少人常犯的错误是：依然分配具有16个地址空间的子网，而忘记了给网关分配地址。这样就错误了，因为：
14＋1＋1＋1＝17
17大于16，所以我们只能分配具有32个地址（32等于2的5次方）空间的子网。这时子网掩码为：255.255.255.224。

## Private IP
```
start,end,cidr,scope,note
0.0.0.0,0.255.255.255,0.0.0.0/8,software                        # 用于广播信息到当前主机
10.0.0.0,10.255.255.255,10.0.0.0/8,Private,                     # A类私有地址
100.64.0.0,100.127.255.255,100.64.0.0/10,Private                # 64个B类私有地址(运营商级 NAT)
127.0.0.0,127.255.255.255,127.0.0.0/8,host                       # 回环IP，表示本机
128.0.0.0,128.0.255.255,128.0.0.0/16,Private                    # 保留
169.254.0.0,169.254.255.255,169.254.0.0/16,Subnet               # DHCP自动分配B类私有IP地址
172.16.0.0,172.31.255.255,172.16.0.0/12                       # B类私有地址
191.255.0.0,191.255.255.255,191.255.0.0/16                       # C类保留地址
192.0.0.0,192.0.0.255,192.0.0.0/24                              # C类保留地址
192.0.2.0–192.0.2.255,192.0.2.0/24,Documentation                # 分配为用于文档和示例中的“TEST-NET”（测试网）
192.88.99.0–192.88.99.255,192.88.99.0/24
192.168.0.0,192.168.255.255,192.168.0.0/16                       # C类私有IP
198.18.0.0–198.19.255.255,198.18.0.0/15
198.51.100.0–198.51.100.255,198.51.100.0/24
203.0.113.0–203.0.113.255,203.0.113.0/24
240.0.0.0,255.255.255.254,240.0.0.0/4
255.255.255.255,255.255.255.255,255.255.255.255/32                # 广播地址
::,::,::/128                                                    # 未指定地址。
::1,::1,::1/128                                              # 用于到本地主机的环回地址。
::ffff:0:0,::ffff:ffff:ffff,::ffff:0:0/96                        #IPv4映射地址。
100::/64    100:: –100::ffff:ffff:ffff:ffff                  # RFC 6666中废除的前缀。
64:ff9b::0.0.0.0,64:ff9b::255.255.255.255,64:ff9b::/96          # 全球互联网[13]   用于IPv4/IPv6转换。（RFC 6052）
2001::/32   2001::,2001::ffff:ffff:ffff:ffff:ffff:ffff   296   # 全局  用于Teredo通道。
2001:10::/28   2001:10::,2001:1f:ffff:ffff:ffff:ffff:ffff:ffff # 2100   软件    已弃用（先前为ORCHID）。
2001:20::/28   2001:20::,2001:2f:ffff:ffff:ffff:ffff:ffff:ffff # 2100   软件    ORCHIDv2。
2001:db8::/32,2001:db8::,2001:db8:ffff:ffff:ffff:ffff:ffff:ffff # 用于文档和示例源代码中的地址。
2002::,2002:ffff:ffff:ffff:ffff:ffff:ffff:ffff,2002::/162112   # 全球互联网  用于6to4。
fc00:: ,fdff:ffff:ffff:ffff:ffff:ffff:ffff:ffff,fc00::/7        # 专用网络  用于专用网络中的本地通信。
fe80:: ,febf:ffff:ffff:ffff:ffff:ffff:ffff:ffff,fe80::/10       # 链路    用于主机之间的链路本地地址。
ff00:: –ffff:ffff:ffff:ffff:ffff:ffff:ffff:ffff,ff00::/8        # 全球互联网    用于多播地址。 
```

全球IP/ASN由ICANN负责分配和管理，ICANN将部分IP地址分配给地区级的Internet注册机构 (RIR)，然后由这些RIR负责该地区的登记注册服务。
全球一共有5个RIR
APNIC亚太地区的IP分配地址表，全球有五大IP分配机构，其它是AfriNIC非洲地区、ARIN美洲地区、LACNIC拉丁美洲和加勒比海、RIPE欧洲地区。
ISO国家标准码：
http://www.iso.org/iso/home/standards/country_codes/country_names_and_code_elements_txt-temp.htm

## 全球所有域名后缀
https://www.iana.org/domains/root/db
https://data.iana.org/TLD/tlds-alpha-by-domain.txt

根服务器
https://root-servers.org/

IPv6 隧道及映射地址自动识别
请求 IPv6 地址的地理位置信息时，会自动识别地址是否为 Teredo 隧道地址（2001::/32）、6to4 隧道地址（2002::/16）或者 IPv4 到 IPv6 映射地址（::ffff:0.0.0.0/96）。如果是，则会提取出 IPv4 地址进行查询。

A类地址第一位必须为0，
B类地址第一位必须为10 
C类地址第一位必须为110 

Class A 1-126/8（00000001-01111110）特大型网络

Class B 128-191/16（10000000-10111111） 大中型网络

Class C 192-223/24（11000000-11011111） 小型网络

Class D 224-239/8（11100000-11101111） 多播地址

Class E 240-255/8（11110000-11111111） 研究用地址未用于Internet

公有IP地址范围:

A: 0.0.0.1-- 9.255.255.255 & 11.0.0.0--126.255.255.255

B:128.0.0.0--172.15.255.255 & 172.32.0.0--191.255.255.255

C: 192.0.0.0-- 192.167.255.255 &192.169.0.0--223.169.255.255

私有IP地址范围：
```
A:    10.0.0.0    ~ 10.255.255.255     即10.0.0.0/8
B: 172.16.0.0  ~ 172.31.255.255     即172.16.0.0/12
C: 192.168.0.0 ~ 192.168.255.255       即192.168.0.0/16

```
特殊IP地址：

169.254.0.0-169.254.255.255 即169.254.0.0/16，属于B类地址，与DHCP服务器失联时windows系统自动分配的IP地址。

127.0.0.0~127.255.255.255 即127.0.0.0/8，属于A类地址，但它被预留作为回环测试使用，不能用作网络地址。

## QA
##### 为什么会有 GOOGLE.COM、LEVEL3.COM 骨干网等非地域信息出现在库中？
这些字样所在的 IP 段一般是使用了 ANYCAST 技术，或者是路由器所在 IP 段，无法定位到单一地域，就不如如此列出，对于一般使用来说，是不会碰到的，碰到了，也是该网络内部人员在用，加入这种标注，更加符合网络实际情况，也是至今为止，我们所独创的方式。

##### 为什么鹏博士(含长城宽带或电信通)、中国科技网这种网站的 IP 归属地信息在各个库中都各有不同呢？
因为它们基本属于全国一张大网，基本全国走一个或者多个出入口，又因为资费以及政策原因，会对外大量使用 NAT 方式通过外地网络进行访问，而且内部变化又可能比较频繁且没有辅助参考信息，这种情况从外部也很难了解和监测到。国外也有类似情况。


## Database Format
* IP2Location™ LITE IP-COUNTRY-REGION-CITY-LATITUDE-LONGITUDE-ZIPCODE-TIMEZONE Database
https://lite.ip2location.com/database/ip-country-region-city-latitude-longitude-zipcode-timezone
```
Database Fields
Name  Type  Description
ip_from  INT (10)† / DECIMAL (39,0)††  First IP address in netblock.
ip_to    INT (10)† / DECIMAL (39,0)††  Last IP address in netblock.
country_code   CHAR(2)  Two-character country code based on ISO 3166.
country_name   VARCHAR(64)    Country name based on ISO 3166.
region_name    VARCHAR(128)   Region or state name.
city_name   VARCHAR(128)   City name.
latitude    DOUBLE   City latitude. Default to capital city latitude if city is unknown.
longitude   DOUBLE   City longitude. Default to capital city longitude if city is unknown.
zip_code    VARCHAR(30)    ZIP/Postal code.
time_zone   VARCHAR(8)  UTC time zone (with DST supported).
If you are looking for Olson Time Zone, please visit here.
```
* IP2Location™ LITE IP-ASN Database
https://lite.ip2location.com/database/ip-asn
```
Name  Type  Description
ip_from  INT (10)† / DECIMAL (39,0)††  First IP address in netblock.
ip_to    INT (10)† / DECIMAL (39,0)††  Last IP address in netblock.
cidr  VARCHAR(43)    IP address range in CIDR.
asn   VARCHAR(10)    Autonomous system number (ASN)
as    VARCHAR(256)   Autonomous system (AS) name
```
* IP2Proxy™ LITE IP-PROXYTYPE-COUNTRY-REGION-CITY-ISP-DOMAIN-USAGETYPE-ASN-LASTSEEN-THREAT-RESIDENTIAL Database
https://lite.ip2location.com/database/px10-ip-proxytype-country-region-city-isp-domain-usagetype-asn-lastseen-threat-residential
```
Name  Type  Description
ip_from  INT (10)† / DECIMAL (39,0)††  First IP address in netblock.
ip_to    INT (10)† / DECIMAL (39,0)††  Last IP address in netblock.
proxy_type  VARCHAR(3)  Type of proxy
country_code   CHAR(2)  Two-character country code based on ISO 3166.
country_name   VARCHAR(64)    Country name based on ISO 3166.
region_name    VARCHAR(128)   Region or state name.
city_name   VARCHAR(128)   City name.
isp   VARCHAR(256)   Internet Service Provider or company's name.
domain   VARCHAR(128)   Internet domain name associated with IP address range.
usage_type  VARCHAR(11)    Usage type classification of ISP or company

    (COM) Commercial
    (ORG) Organization
    (GOV) Government
    (MIL) Military
    (EDU) University/College/School
    (LIB) Library
    (CDN) Content Delivery Network
    (ISP) Fixed Line ISP
    (MOB) Mobile ISP
    (DCH) Data Center/Web Hosting/Transit
    (SES) Search Engine Spider
    (RSV) Reserved

asn   INT(10)  Autonomous system number (ASN).
as    VARCHAR(256)   Autonomous system (AS) name.
last_seen   INT(10)  Proxy last seen in days.
threat   VARCHAR(128)   Security threat reported.
```

# cidr 无类别域间路由，Classless Inter-Domain Routing 