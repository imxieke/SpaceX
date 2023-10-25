<?php

/*
 * @Author: Cloudflying
 * @Date: 2023-05-28 21:18:44
 * @LastEditTime: 2023-05-28 23:54:01
 * @LastEditors: Cloudflying
 * @Description: IP2Region Driver
 */

use ip2region\XdbSearcher;

 class Ip2Region
 {
	protected $xdb = '';
	public function __construct($xdb)
	{
		$this->xdb = $xdb;
		if ( ! file_exists($this->xdb)) {
			throw new Exception("Ip2Region DB File Not Found", 1);
		}
	}

	// 国家|区域|省份|城市|ISP
	public function find($ip)
	{
		try {
			// 1、加载整个 xdb 到内存。
			$cBuff = XdbSearcher::loadContentFromFile($this->xdb);
			if (null === $cBuff) {
				throw new \RuntimeException("failed to load content buffer from '$this->xdb'");
			}
			// 2、使用全局的 cBuff 创建带完全基于内存的查询对象。
			$searcher = XdbSearcher::newWithBuffer($cBuff);
			// 3、查询
			$res = $searcher->search($ip);
			$data = explode('|', $res);
			$info['country'] = trim($data[0]);
			$info['area'] = trim($data[1]);
			$info['province'] = trim($data[2]);
			$info['city'] = trim($data[3]);
			$info['isp'] = trim($data[4]);
			// print_r($data);
			return $info;
		} catch (\Exception $e) {
			var_dump($e->getMessage());
		}
	}
 }
 