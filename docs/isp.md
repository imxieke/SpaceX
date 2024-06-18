## 国内运营商
电信通 鹏博士 长城宽带 歌华、方正、华数

电信通 AS 已与中移铁通互联，已与中国科技网断开互联

AS19430 原长城宽带AS号，现改为北京息壤，鹏博士子公司
AS9395 国信比林，鹏博士孙公司
部分仅由本自治域广播，多数部分同时由电信or联通or科技网等网络自治域广播。

目前电信通、鹏博士、长城宽带内部网络割接合并已经结束，以上三家企业的网络已合并为一张主干网.
电信通的复杂度在于，根据bgp.he.net的互联图，电信通AS与联通、电信和中国科技网有互联，但与移动、铁通无互联。从技术角度上讲，电信通可视为一个小型的一级运营商。所以若不将其作为一个独立的运营商处理，则电信通的路由将全部由默认路由承担，可能会不太方便。在此我建议，将一楼我提到的AS中包含的IPv4地址作为目前电信通所拥有的全部IP地址，而其他由电信或联通负责路由的IP地址则作为电信或联通的IP地址。

## 电信通provider
一小部分鹏博士、电信通这类二级运营商的IP地址，发现有一些IP并非由这些运营商自己对外做广播，实际由电信、联通这些一级运营商代为广播。 如果将这些运营商单独列出来，可能会让大家误会电信通的IP地址只有这一点。 从技术角度来说，按照实际网络路由做划分也更加合理，如某鹏博士的IP由联通代为广播，那么无论是DNS分域解析还是多出口路由器，认为这个IP就是联通线路也未尝不可。
- AS17964       DXTNET Beijing Dian-Xin-Tong Network Technologies Co., Ltd., CN

- qiniu
- upaiyun
- G-Core Labs
- Stackpath
- CDNify
- Imperva
- Azure Content Delivery Network
- Internap
- ImageEngine
- Medianova
- Rackspace
- FileStack
- PageCDN
- Fastly
- Leaseweb
- appfleet
- CDN.net
- VNCDN
- wao
- Universal
- CDNvideo
- jsDelivr
- Edgecast
- AWS
- Cloudflare
- Akamai
- Netlify
- KeyCDN
- CDN77
- 5centsCDN
- Amazon CloudFront
- Google Cloud
- Cachefly