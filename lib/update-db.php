<?php 

/*
 * @Author: Cloudflying
 * @Date: 2023-10-22 01:11:53
 * @LastEditTime: 2023-10-22 21:33:00
 * @LastEditors: Cloudflying
 * @Description: 
 */

require_once __DIR__ . "/Medoo.php";
require_once __DIR__ . "/helper.php";

$save = dirname(__DIR__) . '/databases/latest/asn.php';

// var_dump(file_exists($save));

$source_ipv6 = dirname(__DIR__). '/databases/source/ip2location/IP2LOCATION-LITE-ASN.IPV6.CSV';

/**
 * 插入数据至数据库
 */
function database()
{
	$sql = new \Medoo\Medoo([
	    'database_type' => 'mysql',
	    'database_name' => 'neoip',
	    'server' 		=> '127.0.0.1',
	    'username' 		=> 'root',
	    'password' 		=> 'Xiaoke960318.',
		'prefix'		=> 'neoip_',
	]);
	// return $data = $sql->insert($table,$data);
	return $sql;
}

function ip2location_ipv4_asn()
{
	$sql = database();
	$source_ipv4 = dirname(__DIR__). '/databases/source/ip2location/IP2LOCATION-LITE-ASN.CSV';
	$handle = fopen($source_ipv4, "r");
	while (!feof($handle)) {
		$lines = fgets($handle);
		$lines = trim($lines);
		if ($lines == null) {
			continue;
		}
		$data = explode(',', $lines);
		$data = [
			'start' => trim(str_replace('"', '', $data[0])),
			'end' => trim(str_replace('"', '', $data[1])),
			'cidr' => trim(str_replace('"', '', $data[2])),
			'asn' => trim(str_replace('"', '', $data[3])),
			'org' => trim(str_replace('"', '', $data[4])),
			'create_time' => time(),
			'update_time' => time(),
		];
		if ($sql->get('ipv4_asn', 'start', ['start' => $data['start']]) == '') {
			if($sql->insert('ipv4_asn', $data))
			{
				echo $data['start'] . " 写入完成" . PHP_EOL;
			}
			else {
				echo $data['start'] . " 写入失败" . PHP_EOL;
			}
		}
	}
	fclose($handle);
}

function ip2location_ipv6_asn()
{
	$sql = database();
	$source_ipv4 = dirname(__DIR__) . '/databases/source/ip2location/IP2LOCATION-LITE-ASN.IPV6.CSV';
	$handle = fopen($source_ipv4, "r");
	while (!feof($handle)) {
		$lines = fgets($handle);
		$lines = trim($lines);
		if ($lines == null) {
			continue;
		}
		$data = explode(',', $lines);
		$data = [
			'start' => trim(str_replace('"', '', $data[0])),
			'end' => trim(str_replace('"', '', $data[1])),
			'cidr' => trim(str_replace('"', '', $data[2])),
			'asn' => trim(str_replace('"', '', $data[3])),
			'org' => trim(str_replace('"', '', $data[4])),
			'create_time' => time(),
			'update_time' => time(),
		];
		if ($sql->get('ipv4_asn', 'start', ['start' => $data['start']]) == '') {
			if ($sql->insert('ipv4_asn', $data)) {
				echo $data['start'] . " 写入完成" . PHP_EOL;
			} else {
				echo $data['start'] . " 写入失败" . PHP_EOL;
			}
		}
	}
	fclose($handle);
}

// 需要将 ipv6 转为整型
function ipinfo_asn()
{
	$sql = database();
	$source_ipv4 = dirname(__DIR__) . '/databases/source/ipinfo/ipinfo-asn.csv';
	$handle = fopen($source_ipv4, "r");
	while (!feof($handle)) {
		$lines = fgets($handle);
		$lines = trim($lines);
		if ($lines == null) {
			continue;
		}
		$data = explode(',', $lines);
		$data = [
			'start' => ip2long(trim(str_replace('"', '', $data[0]))),
			'end' => ip2long(trim(str_replace('"', '', $data[1]))),
			'cidr' => trim(str_replace('"', '', $data[2])),
			'asn' => trim(str_replace('"', '', $data[3])),
			'org' => trim(str_replace('"', '', $data[4])),
			'create_time' => time(),
			'update_time' => time(),
		];
		if ($data['cidr'] == 'asn' && $data['asn'] == 'name' ) {
			continue;
		}
		print_r($data);
		exit;
		// if ($sql->get('ipv4_asn', 'start', ['start' => $data['start']]) == '') {
		// 	if ($sql->insert('ipv4_asn', $data)) {
		// 		echo $data['start'] . " 写入完成" . PHP_EOL;
		// 	} else {
		// 		echo $data['start'] . " 写入失败" . PHP_EOL;
		// 	}
		// }
	}
	fclose($handle);
}

// ip2location_ipv4_asn();
// ip2location_ipv6_asn();

ipinfo_asn();
// echo range2cidr('1.0.128.0,1.0.255.255');