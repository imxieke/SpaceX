## LocationX

### 全国省市县村居委会等信息整理

## 数据来自:
- 统计局
- 民政部
- 高德地图
- 腾讯地图
- 百度地图

民政部
http://www.mca.gov.cn/article/sj/xzqh/2020/
http://xzqh.mca.gov.cn/map
https://www.ip138.com/post/
https://www.tianditu.gov.cn/
http://www.stats.gov.cn/tjsj/tjbz/tjyqhdmhcxhfdm/

## Maps
- http://www.gscloud.cn/search

## Example
https://github.com/xiangyuecn/AreaCity-JsSpider-StatsGov

```SQL
drop table if exists node;

CREATE TABLE node (
    id INT NOT NULL AUTO_INCREMENT,
    parent_id INT NOT NULL,
    `name` VARCHAR(128) NOT NULL,
    `code` VARCHAR(258) NOT NULL,
    `level` int not null,
    url VARCHAR(512) NULL,
    create_time TIMESTAMP DEFAULT now(),
    update_time TIMESTAMP null,
    primary key(id)
)  ENGINE=INNODB;



create index in_parent_id on node (parent_id);

create index in_level on node (level);
```