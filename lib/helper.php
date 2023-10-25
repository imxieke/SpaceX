<?php

/**
 * Http Get Request with proxy
 */
function fetch($url)
{
	$context = stream_context_create(
		array(
			'http' => array(
				'timeout' => 5,
				'proxy' => 'tcp://127.0.0.1:7890',
				'request_fulluri' => true,
			)
		)
	);
	return file_get_contents($url, false, $context);
}

/**
 * 将 /数量 格式 转为 CIDR 格式
 * 输入格式 127.0.0.1/256(数量)
 * @param  string $split 输入数据的分割字符
 * 输出格式 1.0.0.0-1.255.255.255(IP范围)
 */
// function num2range($file,$save,$split = '/')
// {
// 	// 转换 IP 数量为范围
// 	echo "=> Convert IP Number to Mask(CIDR)" . PHP_EOL;
// 	// $ipv4_format_file = $build_dir . ''
// 	$lists = file($file);
// 	file_put_contents($save,""); // 清空已存在数据
// 	foreach ($lists as $key => $ipinfo)
// 	{
// 		$ipinfo = explode($split, trim($ipinfo));
// 		$ip = trim($ipinfo[0]);
// 		$mask = ((32 - (exp_div($ipinfo[1]))));
// 		$ips = $ip . '/' . $mask;
// 		$range = mask2range($ips)['range'];
// 		file_put_contents($save, $range . "\n",FILE_APPEND);
// 	}
// }

/**
 * 将 IP 数量转换为 CIDR 127.0.0.1/256
 */
function num2range($str)
{
	if (strlen($str) < 1 || stristr($str, '/') === false) {
		echo "example: 1.0.0.0/512 , type it Please" . PHP_EOL;
		exit(1);
	}
	list($ip, $num) = explode('/', trim($str));
	$longip = ip2long($ip);
	$total = $num - 1;
	$end = $longip + $total;
	return long2ip($longip) . '-' . long2ip($end);
}

/**
 * 将 IP 数量转换为 CIDR 127.0.0.1/256 
 * 需要将数量 - 1 否则转换后的 IP 多一个
 */
function num2cidr($str)
{
	if (strlen($str) < 1 || stristr($str, '/') === false) {
		echo "example: 1.0.0.0/512 , type it Please" . PHP_EOL;
		exit(1);
	}
	list($ip, $num) = explode('/', trim($str));
	$longip = ip2long($ip);
	$total = $num - 1;
	$end = $longip + $total;
	$range = long2ip($longip) . '-' . long2ip($end);
	return range2cidr($range);
}

/**
 * @param string $ip A human readable IPv4 or IPv6 address.
 * @return string Decimal number, written out as a string due to limits on the size of int and float.
 */
function ipv6_numeric($ip)
{
	$binNum = '';
	foreach (unpack('C*', inet_pton($ip)) as $byte) {
		$binNum .= str_pad(decbin($byte), 8, "0", STR_PAD_LEFT);
	}
	return base_convert(ltrim($binNum, '0'), 2, 10);
}

/**
 * 将 IPV6 转为 纯数字
 */
function ipv62long($ip)
{
	$ip_n = inet_pton($ip);
	$bin = '';
	for ($bit = strlen($ip_n) - 1; $bit >= 0; $bit--) {
		$bin = sprintf('%08b', ord($ip_n[$bit])) . $bin;
	}

	if (function_exists('gmp_init')) {
		return gmp_strval(gmp_init($bin, 2), 10);
	} elseif (function_exists('bcadd')) {
		$dec = '0';
		for ($i = 0; $i < strlen($bin); $i++) {
			$dec = bcmul($dec, '2', 0);
			$dec = bcadd($dec, $bin[$i], 0);
		}
		return $dec;
	} else {
		trigger_error('GMP or BCMATH extension not installed!', E_USER_ERROR);
	}
}

/**
 * 将 纯数字 转为 IPV6
 */
function long2ipv6($dec)
{
	if (function_exists('gmp_init')) {
		$bin = gmp_strval(gmp_init($dec, 10), 2);
	} elseif (function_exists('bcadd')) {
		$bin = '';
		do {
			$bin = bcmod($dec, '2') . $bin;
			$dec = bcdiv($dec, '2', 0);
		} while (bccomp($dec, '0'));
	} else {
		trigger_error('GMP or BCMATH extension not installed!', E_USER_ERROR);
	}

	$bin = str_pad($bin, 128, '0', STR_PAD_LEFT);
	$ip = array();
	for ($bit = 0; $bit <= 7; $bit++) {
		$bin_part = substr($bin, $bit * 16, 16);
		$ip[] = dechex(bindec($bin_part));
	}
	$ip = implode(':', $ip);
	return inet_ntop(inet_pton($ip));
}

/**
 * 验证 IPV4 是否为保留的 IP 范围 是则返回true
 * 不在保留的 IP 范围内
 */
function validate_keep($ip)
{
	return false == filter_var(trim($ip), FILTER_VALIDATE_IP, FILTER_FLAG_NO_RES_RANGE) ? true : false;
}

/**
 * 验证 IPV6 有效性
 * IPv6 地址大小为 128 位。首选 IPv6 地址表示法为 x:x:x:x:x:x:x:x，其中每个 x 是地址的 8 个 16 位部分的十六进制值。
 * IPv6 地址范围从 0000:0000:0000:0000:0000:0000:0000:0000 至 ffff:ffff:ffff:ffff:ffff:ffff:ffff:ffff。
 */
function validate_ipv6($ip)
{
	return false == filter_var(trim($ip), FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) ? false : true;
}

/**
 * 验证 IPV4 有效性 正确返回 true
 * ipv4 范围 0.0.0.0 ~ 255.255.255.255
 */
function validate_ipv4($ip)
{
	return false == filter_var(trim($ip), FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ? false : true;
}

/**
 * 验证 IPV4 是否为私有 是则返回 true
 * RFC 指定的私域 IP
 */
function validate_private($ip)
{
	return false == filter_var(trim($ip), FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE) ? true : false;
}

/**
 * 将 IP 范围转为 IP/MASK 格式
 * 需要将数量 + 1 否则转换后的 IP 少一个
 * 0.0.0.0|0.255.255.255 0.0.0.0-0.255.255.255 0.0.0.0,0.255.255.255
 * @param string $split char - , |
 * @param bool $warning 遇到错误会终止执行
 */
function range2cidr($range, $warning = false)
{
	$split_char = '';
	// 获取分割字符
	if (stristr($range, '-') !== false)
		$split_char = '-';
	elseif (stristr($range, ',') !== false)
		$split_char = ',';
	elseif (stristr($range, '|') !== false)
		$split_char = '|';
	else {
		throw new \Exception("Can't find Split Char\n", 1);
	}

	list($start_ip, $end_ip) = explode($split_char, $range);
	$start_ip = trim($start_ip);
	$end_ip = trim($end_ip);

	$total_ip = (ip2long($end_ip) - ip2long($start_ip)) + 1;
	$mask = exp_div($total_ip, 2);

	if ($mask == '' || $mask == 0) {
		if ($warning) {
			echo $range . ' False' . PHP_EOL;
			exit;
		} else {
			$mask = 0;
		}
	}

	$mask = 32 - $mask;
	return $start_ip . '/' . $mask;
}

/**
 * 将 CIDR 格式 转为范围
 * 输入格式 127.0.0.1/256(数量)
 * @param  string $split 输入数据的分割字符
 * 输出格式 CIDR,1.0.0.0-1.255.255.255(IP范围),16581375(可用数量)
 */
function cidr2range($file, $save, $split = '/')
{
	// 转换 CIDR为数量
	echo "=> Convert IP Number to Mask(CIDR)" . PHP_EOL;
	// $ipv4_format_file = $build_dir . ''
	$lists = file($file);
	file_put_contents($save, ""); // 清空已存在数据
	foreach ($lists as $key => $ipinfo) {
		$ipinfo = trim($ipinfo);
		// $ipinfo = explode($split, trim($ipinfo));
		// $ip = trim($ipinfo[0]);
		// $ips = $ipinfo[0] . '/' . $ipinfo[1];
		$range = mask2range($ipinfo)['range'];
		file_put_contents($save, $range . "\n", FILE_APPEND);
	}
}

/**
 * Mask To IP Range
 * @param startip 	第一个 IP
 * @param endip 	第一个 IP
 * @param firstip 	第一个 IP
 * @param last 		最后 IP
 * @param broadcast 广播 IP
 * @param range 	IP 范围
 * @param total 	包含所有 IP
 */
function mask2range($range)
{
	list($ip, $mask) = explode('/', $range);
	if ($mask < 8 || $mask > 31)
		return false;
	$ips = explode('.', $ip);
	$prefix_bit = 32 - intval($mask);

	// Convert to bin IP
	$binip = ip2bin($ip);
	$binip = str_replace('.', '', $binip);
	// 进行掩码反转
	$binip = substr($binip, 0, (-1 * $prefix_bit));
	$startip = $binip;
	for ($i = 0; $i < $prefix_bit; $i++) {
		$binip .= '1';
		$startip .= '0';
	}

	$startip = bin2ip($startip);
	$endip = bin2ip($binip);

	$firstip_lists = explode('.', $startip);
	$firstip_prefix = ((int) $firstip_lists[3]) + 1;
	$firstip = $firstip_lists[0] . '.' . $firstip_lists[1] . '.' . $firstip_lists[2] . '.' . $firstip_prefix;

	$lastip_lists = explode('.', $endip);
	$lastip_prefix = ((int) $lastip_lists[3]) - 1;
	$lastip = $lastip_lists[0] . '.' . $lastip_lists[1] . '.' . $lastip_lists[2] . '.' . $lastip_prefix;

	// 分割新的 ip 反转得到掩码
	$data = [
		'start' => $startip,
		'end' => $endip,
		'broadcast' => $endip,
		'firstip' => $firstip,
		'last' => $lastip,
		'range' => $startip . '-' . $endip,
		'total' => 1 * pow(2, $prefix_bit)
	];

	return $data;
}

/**
 * ipv4 转 二进制 不足八位则补 0
 * 还有种方法就是 转为 long ip base_convert 转换
 */
function ip2bin2($ip)
{
	// echo base_convert(128, 10, 2);exit;
	if (validate_ipv4($ip)) {
		$ips = explode('.', $ip);
		$binip = '';
		foreach ($ips as $key => $subip) {
			(string) $subip = base_convert($subip, 10, 2);
			$strlen = 8 - strlen($subip);
			if (strlen($subip) < 8) {
				for ($i = 0; $i < $strlen; $i++) {
					$subip = '0' . $subip;
				}
			}
			$binip = $binip . '.' . $subip;
		}
		return substr($binip, 1);
	}
}

/**
 * 二进制 转 ipv4
 */
function bin2ip2($binip)
{
	if (strlen($binip) == 32) {
		$ips = str_split($binip, 8);
	} elseif (stristr($binip, '.') !== false) {
		$ips = explode('.', $binip);
	}

	$ip = '';
	foreach ($ips as $key => $subip) {
		$subip = intval($subip);
		$ip = $ip . '.' . base_convert($subip, 2, 10);
	}
	$ip = substr($ip, 1);
	return validate_ipv4($ip) == true ? $ip : false;
}

/**
 * 任意精度数字的除
 * @TODO 计算指数的值 大于给入的则不继续计算 进行报错 或保留精度 避免无限循环 Completed
 * @TODO 指定计算小数后精度
 */
function exp_div($num, $exp = 2)
{
	$result = 0;
	$i = 1;
	while (true) {
		// 计算指数
		$value = pow($exp, $i);
		if ($value == $num) {
			$result = $i;
			break;
		} elseif ($value > $num) {
			$result = false;
			break;
		}
		$i++;
	}
	return $result;
}

/**
 *
 */
function mask2ip($mask)
{
	if ($mask > 32)
		return false;

	$binmask = '';

	for ($i = 0; $i < $mask; $i++) {
		$binmask .= 1;
	}

	// 若二进制掩码不足 32 位则补 0

	if (strlen($binmask) < 32) {
		for ($i = 0; $i < (32 - $mask); $i++) {
			$binmask .= 0;
		}
	}

	// echo $binmask;
	var_dump($binmask);
	// $mask = base_convert($mask, 10, 2);
	// echo $mask;
}

// 获取所有 ASN 不包含其他信息
function get_all_asn()
{
	$latest_dir = dirname(__DIR__) . '/databases/latest';
	$dbdir = dirname(__DIR__) . '/databases/source';
	if (!file_exists('/tmp/asn-all.txt')) {
		$ip2l_file = $dbdir . '/IP2LOCATION-LITE-ASN.CSV';
		$dbip_file = $dbdir . '/dbip-asn.csv';
		$geolite2_v4_file = $dbdir . '/GeoLite2-ASN-Blocks-IPv4.csv';
		$geolite2_v6_file = $dbdir . '/GeoLite2-ASN-Blocks-IPv6.csv';
		$geolite2_file = $dbdir . '/GeoLite2-ASN-Blocks-IPv*.csv';
		$ripe_asn = shell_exec("awk -F ' ' '{print $1}' " . $dbdir . '/ripe-asn');
		$dbip = shell_exec("awk -F ',' '{print $3}' $dbip_file | sort --human-numeric-sort | uniq");
		$ip2location = shell_exec("awk -F ',' '{print $4}' $ip2l_file | grep -v '-' | sed 's#\"##g' | sort --human-numeric-sort | uniq");
		$geo = shell_exec("awk -F ',' '{print $2}' $geolite2_file | sort --human-numeric-sort  | uniq");
		$asn = $ripe_asn . "\n" . $dbip . "\n" . $ip2location . "\n" . $geo;
		file_put_contents('/tmp/asn-all.txt', $asn);
	}
	$asns = shell_exec("cat /tmp/asn-all.txt | grep -v 'autonomous_system_number' | sort --human-numeric-sort | uniq");
	file_put_contents($latest_dir . '/asn.txt', $asns);
}

function filter_asn($config)
{
	$dbdir = dirname(__DIR__) . '/databases/source';
	$lists = [
		// 'ripe' => $dbdir . '/ripe-asn',
		'dbip' => $dbdir . '/dbip-asn.csv',
		'geolite2-ipv4' => $dbdir . '/GeoLite2-ASN-Blocks-IPv4.csv',
		'geolite2-ipv6' => $dbdir . '/GeoLite2-ASN-Blocks-IPv4.csv',
		'ip2location' => $dbdir . '/IP2LOCATION-LITE-ASN.CSV'
	];
	foreach ($lists as $key => $val) {
		if (file_exists($val)) {
			$func = "parse_" . $key . "_asn";
			echo $func . PHP_EOL;
			// $func($val);
			exit;
			// parse_ripe_asn($val);
			// eval("parse_$key_asn($val)");
		}
	}
	// var_dump($lists);
}

// 格式化后保存为csv文件
// asn num,org,country
function parse_ripe_asn($file)
{
	$lists = explode("\n", trim(file_get_contents($file)));
	$saveTo = dirname(__DIR__) . '/databases/latest/ripe-asn.csv';
	unlink($saveTo);
	foreach ($lists as $key => $val) {
		$asninfo = explode(" ", trim($val), 2);
		$asn['code'] = $asninfo[0];
		$asn['org'] = trim((explode(",", $asninfo[1]))[0]);
		if (isset((explode(",", $asninfo[1]))[1])) {
			$asn['country'] = trim((explode(",", $asninfo[1]))[1]);
		} else {
			$asn['country'] = '';
		}
		$asn_format = implode(',', $asn);
		file_put_contents($saveTo, $asn_format . "\n", FILE_APPEND);
	}
}

/**
 * 将  longip 转为范围
 * 传入数据格式 longip,longip(start,end)
 */
function longip2range($file, $save, $split = ',')
{
	// 转换 CIDR为数量
	echo "=> Convert LongIP to Range" . PHP_EOL;
	$lists = file($file);
	file_put_contents($save, ""); // 清空已存在数据
	foreach ($lists as $key => $ipinfo) {
		$ipinfo = trim($ipinfo);
		$ipinfo = explode($split, $ipinfo);
		$range = long2ip($ipinfo[0]) . '-' . long2ip($ipinfo[1]);
		file_put_contents($save, $range . "\n", FILE_APPEND);
	}
}

/**
 * 解压所有文件
 */
function uncompress_file()
{
	$date = date('Ymd', time());
	$dir = __DIR__ . '/databases/source/';
	// unar -f geolie2-asn-csv.zip
	// unar -f geolie2-city-csv.zip
	// unar -f geolie2-country-csv.zip
	// mv GeoLite2-*/*.csv .
	// find . -type d -d 1 -exec rm -fr + {} \ ;
	// find . -type d -d 1 -iname 'GeoLite2*' -exec rm -fr + {} \;
	// unar -f dbip-asn.mmdb.gz
	// unar -f dbip-city.mmdb.gz
	// unar -f dbip-country.mmdb.gz
	// unar -f ip2location-lite-asn-csv.zip
	// mv ip2location-lite-asn-csv/*.CSV  .
	// find . -type d -d 1 -exec rm -fr + {} \;
	$lists = [
		"qqwry/qqwry-$date.dat",
		"geolite2/GeoLite2-ASN-CSV-$date.zip",
		"geolite2/GeoLite2-City-CSV-$date.zip",
		// "geolite2/GeoLite2-Country-CSV-$date.zip",
		"ip2location/DB11LITE-$date.zip",
		"ip2location/DB11LITEIPV6-$date.zip",
		"ip2location/DBASNLITE-$date.zip",
		"ip2location/DBASNLITEIPV6-$date.zip",
		"ip2location/PX10LITE-$date.zip",
		"ip2location/PX10LITEIPV6-$date.zip",
		"rir/afrinic-$date.txt",
		"rir/lacnic-$date.txt",
		"rir/ripe-$date.txt",
		"rir/apnic-$date.txt",
		"rir/arin-$date.txt",
		"rir/ripe-asnames-$date.txt",
		"rir/as1221-asnames-$date.txt",
		"rir/as6447-asnames-$date.txt"
	];

	foreach ($lists as $key => $val) {
		$path = $dir . $val;
		$vals = explode('/', $val);
		$ipdir = $vals[0];
		$file = $vals[1];
		if ($ipdir == 'geolite2' || $ipdir == 'ip2location') {
			echo "==> uncompress $file \n";
			$filename = str_replace('.zip', '', $file);
			$fpath = $dir . $ipdir;
			echo shell_exec("cd $fpath;unzip -qo $file");
		}
	}
	$geolite2_dir = $dir . '/geolite2';
	shell_exec("cd $geolite2_dir;mv */*.csv .");
}

/**
 * 获取全球所有 IP
 * 数据源  GeoLite2 // NIC(APNIC AFRINIC ARIN LACNIC RIPE)  Ip2Location
 */
function build_global_ip($force = true)
{
	$date_today = DATE_TODAY;
	$dir = DATA_DIR . '/source';
	$build_dir = DATA_DIR . '/latest/';
	$ipv4 = $build_dir . "/ipv4_$date_today.txt";
	$ipv6 = $build_dir . "/ipv6_$date_today.txt";
	file_put_contents($ipv4, '');
	file_put_contents($ipv6, '');

	// Build Geolite2
	$geo_v4_file = $dir . '/geolite2/GeoLite2-City-Blocks-IPv4.csv';
	$geo_v6_file = $dir . '/geolite2/GeoLite2-City-Blocks-IPv6.csv';

	echo "==> Filter GeoLite IP \n";
	$geo_v4_cmd = <<<STD
cat $geo_v4_file | grep -v 'network' | awk -F ',' '{print $1}' > $ipv4
STD;
	$geo_v6_cmd = <<<STD
cat $geo_v6_file | grep -v 'network' | awk -F ',' '{print $1}' > $ipv6
STD;
	shell_exec($geo_v4_cmd);
	shell_exec($geo_v6_cmd);

	echo "Congratulation All is Done " . PHP_EOL;
}

/**
 * 下载构建所需文件
 * 文件列表:
 * ip2location : Country ASN(V4 V6) DB11(IP 信息库)
 * geolite2
 */
function get_all_source($config = [])
{
	$saveTo = CUR_DIR . '/databases/source/';
	$date = date('Ymd', time());
	foreach ($config['db'] as $name => $url) {
		$path = $saveTo . $name;
		if (!file_exists($path)) {
			echo "==> fetch $url Save as $path \n";
			$content = fetch($url);
			if (strlen($content) > 0) {
				file_put_contents($saveTo . $name, $content);
			} else {
				echo "==> fetch $saveTo failed \n";
			}
		}
	}
	foreach ($config['rir'] as $name => $url) {
		$path = $saveTo . 'rir/' . $name;
		if (!file_exists($path)) {
			echo "==> fetch $url Save as $path \n";
			;
			$content = fetch($url);
			if (strlen($content) > 0) {
				file_put_contents($path, $content);
			} else {
				echo "==> fetch $saveTo failed \n";
			}
		}
	}
	echo 'All IP FILE File is Done' . PHP_EOL;

}

/**
 * 生成 CSV 格式 IP 信息
 */
function gennerate()
{
	$format_title = 'start,end,country,province,city,area,isp,lat,lon';
}

/**
 * 构建 ASN Number
 * 数据源  NIC(APNIC AFRINIC ARIN LACNIC RIPE) GeoLite2 Ip2Location IPIPNet BGP AS1221 AS6447 Ripe ASN
 */
function build_asn_num()
{
	$data_dir = DATA_DIR . '/source';
	$build_dir = DATA_DIR . '/latest/';
	$tmp_asn = '/tmp/tmp_asn_num.csv';

	$asn_num_file = $build_dir . 'asn_num_' . DATE_TODAY . '.csv';
	$geo_asn = $data_dir . '/geolite2/GeoLite2-ASN-*.csv';
	$ip2loc_asn = $data_dir . '/ip2location/IP2LOCATION-LITE-ASN*.CSV';
	$ipip_asn = $data_dir . '/ipip_asn.csv';

	$apnic_asn = $data_dir . '/rir/apnic-' . TODAY . '.txt';
	$afrinic_asn = $data_dir . '/rir/afrinic-' . TODAY . '.txt';
	$arin_asn = $data_dir . '/rir/arin-' . TODAY . '.txt';
	$lacnic_asn = $data_dir . '/rir/lacnic-' . TODAY . '.txt';
	$ripe_asn = $data_dir . '/rir/ripe-' . TODAY . '.txt';
	$bgp_asn = $data_dir . '/bgp_asn.csv';

	// Build APNIC AFRINIC ARIN LACNIC RIPE ASN
	// 检测 IPIP DB 是否存在
	if (
		file_exists($apnic_asn) &&
		file_exists($afrinic_asn) &&
		file_exists($arin_asn) &&
		file_exists($lacnic_asn) &&
		file_exists($ripe_asn)
	) {
		echo "==> 开始过滤 Nic: (APNIC AFRINIC ARIN LACNIC RIPE) ASN Num \n";
		$cmd = <<<STD
cat $apnic_asn $afrinic_asn $arin_asn $lacnic_asn $ripe_asn | grep -v '*' | grep asn | awk -F '|' '{print $4}' | sort --human-numeric-sort | uniq
STD;
		$nic_asn_lists = shell_exec($cmd);
		file_put_contents($tmp_asn, $nic_asn_lists, FILE_APPEND);
	}


	// 检测 IPIP DB 是否存在
	if (file_exists($ipip_asn)) {
		echo "==> 开始过滤 IPIP ASN Num \n";
		$cmd = <<<STD
cat $ipip_asn | awk -F '|' '{print $1}' | sort --human-numeric-sort | uniq
STD;

		$ipip_asn_lists = shell_exec($cmd);
		file_put_contents($tmp_asn, $ipip_asn_lists, FILE_APPEND);
		// echo $ipip_asn_lists;
	}

	// 检测 BGP DB 是否存在
	if (file_exists($bgp_asn)) {
		echo "==> 开始过滤 BGP ASN Num \n";
		$cmd = <<<STD
	cat $bgp_asn | awk -F ',' '{print $1}' | sort --human-numeric-sort | uniq
STD;

		$bgp_asn_lists = shell_exec($cmd);
		file_put_contents($tmp_asn, $bgp_asn_lists, FILE_APPEND);
		// echo $bgp_asn_lists;
	}

	// 检测 GeoLite2 DB 是否存在
	if (
		file_exists($data_dir . '/geoLite2/GeoLite2-ASN-Blocks-IPv4.csv')
		&&
		file_exists($data_dir . '/geoLite2/GeoLite2-ASN-Blocks-IPv6.csv')
	) {
		echo "==> 开始过滤 GeoLite2 ASN Num \n";
		$cmd = <<<STD
	cat $geo_asn | grep -v 'autonomous_system_number' | awk -F ',' '{print $2}' | sort --human-numeric-sort | uniq
STD;

		$geo_asn_lists = shell_exec($cmd);
		file_put_contents($tmp_asn, $geo_asn_lists, FILE_APPEND);
		// echo $geo_asn_lists;
	}

	// 检测 IP2Location DB 是否存在
	if (
		file_exists($data_dir . '/ip2location/IP2LOCATION-LITE-ASN.CSV')
		&&
		file_exists($data_dir . '/ip2location/IP2LOCATION-LITE-ASN.IPV6.CSV')
	) {
		echo "==> 开始过滤 IP2Location ASN Num \n";
		$cmd = <<<STD
cat $ip2loc_asn | awk -F '"' '{print $8}' | grep -v '-' | sort --human-numeric-sort | uniq
STD;

		$ip2loc_asn_lists = shell_exec($cmd);
		file_put_contents($tmp_asn, $ip2loc_asn_lists, FILE_APPEND);
		// echo $ip2loc_asn_lists;
	}

	echo "==> 数据完成合并, 开始过滤 \n";
	$cmd = shell_exec("cat $tmp_asn | sort --human-numeric-sort | uniq > $asn_num_file");
	unlink($tmp_asn);
	echo "==> ASN 数据过滤完成 \n";
}

/**
 * 合并 ASN
 * 数据源: GeoLite2 Ip2Location IPIPNet BGP ASN
 */
function build_asn()
{
	$rir_dir = DATA_DIR . '/source/rir';
	$build_dir = DATA_DIR . '/latest/';

	if (!is_dir('/tmp/locationx')) {
		mkdir('/tmp/locationx', 0755, true);
		chmod('/tmp/locationx', 0755);
	}
	$asn_num = $build_dir . 'asn-num-' . DATE_TODAY . '.csv';
	$asn_num_tmp = $build_dir . 'asn-num-tmp' . DATE_TODAY . '.csv';

	// 合并 AS1221 AS6447 Ripe ASN
	if (!file_exists($asn_num)) {
		file_put_contents($asn_num_tmp, '');

		$lists = [
			'as1221' => $rir_dir . '/as1221-' . DATE_TODAY . '.txt',
			'as6447' => $rir_dir . '/as6447-' . DATE_TODAY . '.txt',
			'ripeasn' => $rir_dir . '/ripeasn-' . DATE_TODAY . '.txt',
		];

		foreach ($lists as $key => $asn_file) {
			echo 'Fetch ' . $key . ' ASN File ' . PHP_EOL;
			$asn_lists = file($asn_file);
			foreach ($asn_lists as $key => $asn) {
				$asn1 = explode(' ', $asn);
				$asn2 = explode(',', $asn);
				$asn_info = [
					'asn' => trim(str_replace('AS', '', $asn1[0])),
					'org' => trim(str_replace($asn1[0], '', $asn2[0])),
					'country' => trim(@$asn2[1])
				];

				if ($asn_info['org'] != '--No Registry Entry--') {
					$asn_info = implode(',', $asn_info);
					file_put_contents($asn_num_tmp, $asn_info . "\n", FILE_APPEND);
				}
			}
		}
		$asn_cmd = <<<STD
cat $asn_num_tmp | sort --human-numeric-sort | uniq > $asn_num
STD;
		shell_exec($asn_cmd);
		unlink($asn_num_tmp);
	}

	// 获取 Geolite2 的 IPV4 IPV6 ASN
	$new_geolite2_asn_file = '/tmp/locationx/geolite2_asn_num_' . DATE_TODAY . '.csv';
	if (!file_exists($new_geolite2_asn_file)) {
		echo "Build Geolite2 ASN \n";
		$geolite2_ipv4_asn_file = DATA_DIR . '/source/geolite2/GeoLite2-ASN-Blocks-IPv4.csv';
		$geolite2_ipv6_asn_file = DATA_DIR . '/source/geolite2/GeoLite2-ASN-Blocks-IPv6.csv';
		$cmd = "grep -v 'autonomous_system_number' " . $geolite2_ipv4_asn_file . ' ' . $geolite2_ipv6_asn_file;
		$cmd = $cmd . " | awk -F ',' '{print $2}' | sort --human-numeric-sort | uniq > " . $new_geolite2_asn_file;
		shell_exec($cmd);
	}

	// 合并 ip2location ipv4 ipv6 ASN
	$new_ip2location_asn = '/tmp/locationx/ip2location_asn_num_' . DATE_TODAY . '.csv';
	if (!file_exists($new_ip2location_asn)) {
		echo "Merge Ip2Location IPV4 IPV6 ASN \n";
		$ip2location_asn_tmp = '/tmp/locationx/ip2location_asn.csv';
		$ip2location_ipv4_asn = DATA_DIR . '/source/ip2location/IP2LOCATION-LITE-ASN.CSV';
		$ip2location_ipv6_asn = DATA_DIR . '/source/ip2location/IP2LOCATION-LITE-ASN.IPV6.CSV';
		$cmd = "cat $ip2location_ipv4_asn $ip2location_ipv6_asn | awk -F ',' '{print $4}' | uniq > " . $ip2location_asn_tmp;
		shell_exec($cmd);
		$asn_data = file($ip2location_asn_tmp);

		$ip2location_asn_tmp_format = '/tmp/locationx/ip2location_asn_format.csv';
		if (file_exists($ip2location_asn_tmp_format))
			unlink($ip2location_asn_tmp_format);
		foreach ($asn_data as $key => $asninfo) {
			$asninfo = str_replace('"', '', trim($asninfo));
			if ($asninfo != '-' && $asninfo != 0) {
				file_put_contents($ip2location_asn_tmp_format, $asninfo . "\n", FILE_APPEND);
			}
		}

		$format_cmd = "cat $ip2location_asn_tmp_format | sort --human-numeric-sort | uniq > " . $new_ip2location_asn;
		shell_exec($format_cmd);
	}

	// 获取 ipipnet ASN
	$new_ipip_asn_file = '/tmp/locationx/ipip_asn_num_' . DATE_TODAY . '.csv';
	if (!file_exists($new_ipip_asn_file)) {
		echo "Build IPIP ASN \n";
		$ipip_asn_file = DATA_DIR . '/source/ipip_asn.csv';
		if (file_exists($ipip_asn_file)) {
			$cmd = "cat " . $ipip_asn_file . " | awk -F ',' '{print $1}' | uniq > " . $new_ipip_asn_file;
			shell_exec($cmd);
		} else {
			echo "ipip ASN Info csv file Not Found, Build it first \n";
		}
	}

	// 获取 BGP ASN
	$new_bgp_asn_file = '/tmp/locationx/bgp_asn_num_' . DATE_TODAY . '.csv';
	if (!file_exists($new_bgp_asn_file)) {
		echo "Build IPIP ASN \n";
		$bgp_asn_file = DATA_DIR . '/source/bgp_asn.csv';
		if (file_exists($bgp_asn_file)) {
			$cmd = "cat " . $bgp_asn_file . " | awk -F ',' '{print $1}' | uniq > " . $new_bgp_asn_file;
			shell_exec($cmd);
		} else {
			echo "BGP ASN Info csv file Not Found, Build it first \n";
		}
	}

	// 合并 ASN (排序 去重)
	$new_asn_file = '/tmp/locationx/asn_num_' . DATE_TODAY . '.csv';
	if (!file_exists($new_asn_file))
	// if(file_exists($new_geolite2_asn_file) && file_exists($new_ipip_asn_file))
	{
		$tmp_asn_num = '/tmp/locationx/asn_num_tmp.csv';
		echo "Merge Geolite2 ASN And IPIP Net ASN \n";
		$cmd = "cat $new_geolite2_asn_file $new_ipip_asn_file $new_ip2location_asn > " . $tmp_asn_num;
		shell_exec($cmd);

		$filter_cmd = "cat $tmp_asn_num | sort --human-numeric-sort | uniq > " . $new_asn_file;
		shell_exec($filter_cmd);
		unlink($tmp_asn_num);
	}
	rename($new_asn_file, __DIR__ . '/databases/source/asn_num.csv');
	shell_exec('rm -fr /tmp/locationx/*');
}

function build_bgp_asn()
{
	echo "Build BGP ASN \n";
	$url = 'https://www.xie.ke/spider/net/bgpAsnLocal';
	$tmp_file = '/tmp/bgp_asn_tmp_' . DATE_TODAY . '.csv';
	if (!file_exists($tmp_file)) {
		echo "==> Get BGP ASN File \n";
		$data = file_get_contents($url);
		file_put_contents($tmp_file, $data);
	}
	$lists = file($tmp_file);
	$bgp_asn_file = DATA_DIR . '/source/bgp_asn.csv';
	if (file_exists($bgp_asn_file))
		unlink($bgp_asn_file);
	echo "==> Build BGP ASN Number File \n";
	foreach ($lists as $key => $asninfo) {
		$asninfo = explode(',', $asninfo);
		$asninfo[0] = trim(str_replace('AS', '', $asninfo[0]));
		$cc = trim($asninfo[2]);
		$org = trim($asninfo[1]);
		$asninfo[1] = $cc;
		$asninfo[2] = $org;
		$asninfo = implode(',', $asninfo);
		file_put_contents($bgp_asn_file, $asninfo . "\n", FILE_APPEND);
	}
	if (file_exists($tmp_file))
		unlink($tmp_file);
}

/**
 * 将 爬虫爬取的 IPIP ASN json 数据转为 CSV
 */
function ipip_asn()
{
	$url = 'http://xie.ke/spider/net/ipipAsn';
	$file = DATA_DIR . '/source/ipip_asn.json';
	$tmpfile = '/tmp/ipip_asn_tmp.csv';
	// $newfile = DATA_DIR . '/source/ipip_asn_' . DATE_TODAY . '.csv';
	$newfile = DATA_DIR . '/source/ipip_asn.csv';

	if (!file_exists($file))
		file_put_contents($file, file_get_contents($url));

	if (file_exists($newfile)) {
		return "IPIP ASN " . DATE_TODAY . ' file Completed ' . PHP_EOL;
	}
	$data = json_decode(file_get_contents($file), true);
	foreach ($data as $key => $asninfo) {
		$asninfo['asn'] = trim(str_replace('AS', '', $asninfo['asn']));
		$asninfo['name'] = trim($asninfo['name']);
		$asninfo['location'] = trim($asninfo['location']);
		$asninfo['country'] = trim($asninfo['country']);
		$asninfo = implode('|', $asninfo);
		file_put_contents($tmpfile, $asninfo . "\n", FILE_APPEND);
	}
	shell_exec('sort --human-numeric-sort ' . $tmpfile . ' > ' . $newfile);
	unlink($tmpfile);
}

/**
 * 将 ipregion 数据数据精简 数据精确到 市
 * format _城市Id|国家|区域|省份|城市|ISP_
 * start | end | 国家 |未知|广东省|广州市|电信
 */
function ip2region_format()
{
	$file = dirname(__DIR__) . '/databases/ip.merge.txt';
	$newfile = dirname(__DIR__) . '/databases/build/ip2region.txt';
	$format = "ip,country,province,city,isp \n";
	file_put_contents($newfile, $format);
	$file = file($file);
	foreach ($file as $key => $ipinfo) {
		$ip_key = explode('|', $ipinfo);
		if (trim($ip_key[2]) == '0')
			$ip_key[2] = '';
		if (trim($ip_key[4]) == '0')
			$ip_key[4] = '';
		if (trim($ip_key[5]) == '0')
			$ip_key[5] = '';
		if (trim($ip_key[6]) == '0')
			$ip_key[6] = '';
		// $newipinfo = range2ip($ip_key[0] . ',' . $ip_key[1]) .',' . $ip_key[2].',' . $ip_key[4].',' . $ip_key[5].',' . $ip_key[6];
		$newipinfo = $ip_key[0] . ',' . $ip_key[1] . ',' . $ip_key[2] . ',' . $ip_key[4] . ',' . $ip_key[5] . ',' . $ip_key[6];
		$newipinfo = trim($newipinfo);
		file_put_contents($newfile, $newipinfo . "\n", FILE_APPEND);
	}
	echo "ip2Region Build Completed \n";
}

/**
 * ip2location lite asn 优化精简
 */
function ip2location_asn()
{
	$file = dirname(__DIR__) . '/databases/IP2LOCATION-LITE-ASN.CSV';
	$newfile = dirname(__DIR__) . '/databases/build/ip2location-lite-asn.txt';
	$format = "cidr,asn,as \n";
	file_put_contents($newfile, $format);
	$file = file($file);
	foreach ($file as $key => $ipinfo) {
		$ip_key = explode(',', $ipinfo);
		if (trim($ip_key[3]) == '"-"')
			$ip_key[3] = '';
		if (trim($ip_key[4]) == '"-"')
			$ip_key[4] = '';
		// print_r($ip_key);exit;
		$newipinfo = $ip_key[2] . ',' . $ip_key[3] . ',' . $ip_key[4];
		$newipinfo = trim($newipinfo);
		$newipinfo = str_replace('"', '', $newipinfo);
		// echo $newipinfo . PHP_EOL;
		file_put_contents($newfile, $newipinfo . "\n", FILE_APPEND);
		if ($key == 10) {
			// exit;
		}
	}
	echo "ip2Location asn build Completed \n";
}

/**
 * ip2location lite db1 优化精简
 * latitude,longitude 默认是首都
 */
function ip2location_db11()
{
	$file = dirname(__DIR__) . '/databases/IP2LOCATION-LITE-DB11.CSV';
	$newfile = dirname(__DIR__) . '/databases/build/ip2location-lite-db11.txt';
	$format = "cidr,country_code,country_name,region_name,city_name,latitude,longitude,zip_code,timezone \n";
	file_put_contents($newfile, $format);
	$file = file($file);
	foreach ($file as $key => $ipinfo) {
		$ipinfo = str_replace('"', '', $ipinfo);
		$ip_key = explode(',', $ipinfo);
		$ip_key[0] = long2ip((int) trim($ip_key[0]));
		$ip_key[1] = long2ip((int) trim($ip_key[1]));
		if (trim($ip_key[2]) == '-')
			$ip_key[2] = '';
		if (trim($ip_key[3]) == '-')
			$ip_key[3] = '';
		if (trim($ip_key[4]) == '-')
			$ip_key[4] = '';
		if (trim($ip_key[5]) == '-')
			$ip_key[5] = '';
		if (trim($ip_key[6]) == '0.000000')
			$ip_key[6] = '';
		if (trim($ip_key[7]) == '0.000000')
			$ip_key[7] = '';
		if (trim($ip_key[8]) == '-')
			$ip_key[8] = '';
		if (trim($ip_key[9]) == '-')
			$ip_key[9] = '';
		// print_r($ip_key);
		$newipinfo = $ip_key['0'] . ',' . $ip_key[1];

		$newipinfo .= ',' . $ip_key[2] . ',' . $ip_key[3] . ',' . $ip_key[4] . ',' . $ip_key[5] . ',' . $ip_key[6] . ',' . $ip_key[7] . ',' . $ip_key[8] . ',' . $ip_key[9];
		$newipinfo = trim($newipinfo);
		// echo $newipinfo . PHP_EOL;
		file_put_contents($newfile, $newipinfo . "\n", FILE_APPEND);
		if ($key == 10) {
			// exit;
		}
	}
	echo "ip2Location asn build Completed \n";
}

/**
 * 构建 IP 段列表 目前比较全的 IP 数据
 */
function build_geolite2_ipv4()
{
	$geolite2 = 'databases/source/geolite2/GeoLite2-City-Blocks-IPv4.csv';
	$geolite2_ipv4 = 'databases/latest/ipv4_range.csv';
	file_put_contents($geolite2_ipv4, '');
	$geolite2_lists = file($geolite2);
	unset($geolite2_lists[0]);
	$geolite2_count = count($geolite2_lists);
	foreach ($geolite2_lists as $key => $ip) {
		echo "Now is $key surplus : " . ($geolite2_count - $key) . PHP_EOL;
		$ip = explode(',', $ip);
		$ip = $ip[0];
		$ip = mask2range($ip);
		$ip = $ip['range'];
		$ip = str_replace('-', ',', $ip);
		file_put_contents($geolite2_ipv4, $ip . "\n", FILE_APPEND);
	}
}

/**
 * 数据比较简陋 不完整
 */
function build_ip2location_ipv4()
{
	$ip2location = 'databases/source/ip2location/IP2LOCATION-LITE-DB11.CSV';
	$ip2location_ipv4 = 'databases/latest/ip2location_ipv4.csv';
	file_put_contents($ip2location_ipv4, '');
	$ip2location_lists = file($ip2location);
	$ip2location_count = count($ip2location_lists);
	$data = '';
	foreach ($ip2location_lists as $key => $ip) {
		echo "Now is $key surplus : " . ($ip2location_count - $key) . PHP_EOL;
		$ip = str_replace('"', '', $ip);
		$ip = explode(',', $ip);
		$start_ip = long2ip($ip[0]);
		$end_ip = long2ip($ip[1]);
		$range = $start_ip . ',' . $end_ip . "\n";
		file_put_contents($ip2location_ipv4, $range, FILE_APPEND);
		// $data = $data . $range;
	}
	// file_put_contents($ip2location_ipv4,$data . "\n",FILE_APPEND);
}

/**
 * 合并 ASN 信息
 */
function merge_asn_info()
{
	$dir = __DIR__ . '/databases/source/';
	$asn = $dir . 'asn_num.csv';
	$newasn = $dir . 'asn.csv';
	$bgp = $dir . 'bgp_asn.csv';
	$ipip = $dir . 'ipip_asn.csv';
	$ip2lv4 = $dir . 'ip2Location/IP2LOCATION-LITE-ASN.CSV';
	$ip2lv6 = $dir . 'ip2Location/IP2LOCATION-LITE-ASN.IPV6.CSV';
	$geoasnv4 = $dir . 'geolite2/GeoLite2-ASN-Blocks-IPv4.csv';
	$geoasnv6 = $dir . 'geolite2/GeoLite2-ASN-Blocks-IPv6.csv';

	$asn_lists = file($asn);
	$bgp_lists = file($bgp);
	$ipip_lists = file($ipip);
	$ip2lv4_lists = file($ip2lv4);
	$ip2lv6_lists = file($ip2lv6);
	$geoasnv4_lists = file($geoasnv4);
	$geoasnv6_lists = file($geoasnv6);
	foreach ($asn_lists as $key => $asn) {
		$asn = (int) trim($asn);
		var_dump($asn);
		exit;
	}

	$mem_usage = round((memory_get_usage() - MEM_USAGE) / 1024 / 1024, 3);
	echo 'Memory Usage： ' . $mem_usage . ' MB' . PHP_EOL;
}

/**
 * 插入数据至数据库
 */
function insert($table = '', $data = [])
{
	$sql = new \Medoo\Medoo([
		'database_type' => 'mysql',
		'database_name' => 'locationx',
		'server' => '127.0.0.1',
		'username' => 'root',
		'password' => '19960318'
	]);
	$data = $sql->insert($table, $data);
}
function update_ips()
{
	$sql = new \Medoo\Medoo([
		'database_type' => 'mysql',
		'database_name' => 'locationx',
		'server' => '127.0.0.1',
		'username' => 'root',
		'password' => '19960318'
	]);
	// $data = $sql->insert($table,$data);

	$file = dirname(__DIR__) . '/databases/latest/ipv4_' . date('Y-m-d', time()) . '.txt';
	$lists = file($file);
	$times = 1;
	$insertTimes = 80000;
	$total = count($lists);
	$data = [];
	foreach ($lists as $key => $ips) {
		$ips = trim($ips);
		if (count($data) == $insertTimes) {
			$data[] = [
				'range' => $ips,
				'create_time' => time(),
				'update_time' => time(),
			];
			echo "Count:" . count($data) . "  Date: " . date('Y-m-d H:i:s', time()) . PHP_EOL;
			$sql->insert('ipv4', $data);
			$times = 0;
			$data = [];
		} elseif ($key == ($total - 1)) {
			$data[] = [
				'range' => $ips,
				'create_time' => time(),
				'update_time' => time(),
			];
			echo "Last Count:" . count($data) . " Date: " . date('Y-m-d H:i:s', time()) . PHP_EOL;
			$sql->insert('ipv4', $data);
			$times = 0;
			$data = [];
		} else {
			$data[] = [
				'range' => $ips,
				'create_time' => time(),
				'update_time' => time(),
			];
			$times++;
		}
	}
	echo 'Completed' . PHP_EOL;
}

function write2db()
{
	$dir = __DIR__ . '/databases/source/';
	$asn = $dir . 'asn_num.csv';
	$asn = file($asn);
	$ipip_asn = $dir . 'ipip_asn.csv';
	foreach ($asn as $key => $asninfo) {
		print_r($asninfo);
		$data = [
			'asn' => $asninfo,
			'create_time' => time()
		];
		insert('asn', $data);
		exit;
	}
}

/**
 * 查询 IP 信息
 */
function find()
{
	$qqwry = __DIR__ . '/../databases/source/chunzhen/chunzhen.dat';

	$geoip = __DIR__ . '/../databases/source/geolite/Geolite2-City.mmdb';
	$geoasn = __DIR__ . '/../databases/source/geolite/Geolite2-ASN.mmdb';

	$ip2region = __DIR__ . '/../databases/source/ip2region/ip2region.xdb';

	$ip2lv4 = __DIR__ . '/../databases/source/ip2location/IP2LOCATION-LITE-DB11.BIN';
	$ip2lv6 = __DIR__ . '/../databases/source/ip2location/IP2LOCATION-LITE-DB11.IPV6.BIN';

	$qw = new Pureip($qqwry);
	$geo = new Geoip([
		'asn' => $geoip,
		'city' => $geoasn
	]);
	$ip2r = new Ip2Region($ip2region);
	$ip2lv4 = new \IP2Location\Database($ip2lv4);
	$ip2lv6 = new \IP2Location\Database($ip2lv6);
	global $argv;
	$engine = isset($argv[2]) ? $argv[2] : 'qqwry';
	$ip = isset($argv[3]) ? $argv[3] : $argv[2];

	if ($ip == '')
		echo 'ip is null' . PHP_EOL;
	$q = $qw->get($ip);
	$ir = $ip2r->get($ip);
	$il = $ip2lv4->lookup($ip);
	$g = $geo->get($ip);

	echo "QQWRY:" . $q['country'] . ' ' . $q['area'] . PHP_EOL;
	echo "IP2Region:" . '' . $ir['country'] . ' ' . $ir['province'] . $ir['city'] . $ir['isp'] . PHP_EOL;
	echo "IP2Location:" . '' . $il['countryName'] . ' ' . $il['regionName'] . ' ' . $il['cityName'] . PHP_EOL;
	echo "GeoLite2:" . '' . @$g['continent']['names']['zh-CN'] . ' ' . @$g['country']['names']['zh-CN'] . ' ' . @$g['subdivisions'][0]['names']['zh-CN'] . ' ' . @$g['city']['names']['zh-CN'] . PHP_EOL;
	// print_r($geo->get($ip));
	exit;
	switch ($engine) {
		case 'cz':
			print_r($qw->get($ip));
			break;
		case 'geo':
			print_r($geo->get($ip));
			break;
		case 'ip2region':
			print_r($ip2r->get($ip));
			break;
		case 'ip2location':
			print_r($ip2lv4->lookup($ip));
			break;
		default:
			print_r($qw->get($ip));
			break;
	}
}

function convert_rir_ipv4_cidr()
{
	$ips = file_get_contents('/tmp/rir-ips-v4.txt');
	$saveTo = dirname(__DIR__) . '/databases/source/rir-ips-v4.txt';
	@unlink($saveTo);
	$ips = explode("\n", $ips);
	foreach ($ips as $ip) {
		$ip = trim($ip);
		if (strlen($ip) > 0)
			file_put_contents($saveTo, num2cidr($ip) . PHP_EOL, FILE_APPEND);
	}
	echo "All Done" . PHP_EOL;
}

function convert_dbip_ip_cidr()
{
	// fec0::,ffff:ffff:ffff:ffff:ffff:ffff:ffff:ffff,EU,CH,Fribourg,Murten/Morat,46.9219,7.16595
	$first = ipv62long('fec0::');
	$end = ipv62long('ffff:ffff:ffff:ffff:ffff:ffff:ffff:ffff');
	$end = gmp_add($end, 1);
	// $total = $end - $first;
	$total = gmp_sub($end, $first);
	echo $first . '-' . $end . ' Total Avaiable: ' . $total . PHP_EOL;
	// var_dump($total);
	// echo "Div " .  exp_div($total,2) . PHP_EOL ;
	echo $total . PHP_EOL;
	echo gmp_pow(2, 120) . PHP_EOL;
}

/**
 * 跟新数据文件
 */
function updateQqWryFile()
{
	$copyWrite = file_get_contents("http://update.cz88.net/ip/copywrite.rar");
	$qqwry = file_get_contents("http://update.cz88.net/ip/qqwry.rar");
	$key = unpack("V6", $copyWrite)[6];
	for ($i = 0; $i < 0x200; $i++) {
		$key *= 0x805;
		$key++;
		$key = $key & 0xFF;
		$qqwry[$i] = chr(ord($qqwry[$i]) ^ $key);
	}
	$qqwry = gzuncompress($qqwry);
	$fp = @fopen($this->file, "wb");
	if ($fp) {
		fwrite($fp, $qqwry);
		fclose($fp);
	}
}

function isIpv4($ip)
{
	return (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) ? true : false;
}

function isIpv6($ip)
{
	return (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) ? true : false;
}

function ipv4ToDecimal($ip)
{
	if (!isIpv4($ip)) {
		return;
	}

	return sprintf('%u', ip2long($ip));
}

function ipv6ToDecimal($ipv6)
{
	if (!isIpv6($ipv6)) {
		return;
	}

	return (string) \gmp_import(inet_pton($ipv6));
}

function decimalToIpv4($number)
{
	if (!preg_match('/^\d+$/', $number)) {
		return;
	}

	if ($number > 4294967295) {
		return;
	}

	return long2ip($number);
}

function decimalToIpv6($number)
{
	if (!preg_match('/^\d+$/', $number)) {
		return;
	}

	if ($number <= 4294967295) {
		return;
	}

	return inet_ntop(str_pad(gmp_export($number), 16, "\0", STR_PAD_LEFT));
}

function ipv4ToCidr($ipFrom, $ipTo)
{
	$s = explode('.', $ipFrom);

	$start = '';
	$dot = '';

	foreach ($s as $val) {
		$start = sprintf('%s%s%d', $start, $dot, $val);
		$dot = '.';
	}

	$end = '';
	$dot = '';

	$e = explode('.', $ipTo);

	foreach ($e as $val) {
		$end = sprintf('%s%s%d', $end, $dot, $val);
		$dot = '.';
	}

	$start = ip2long($start);
	$end = ip2long($end);
	$result = [];

	while ($end >= $start) {
		$maxSize = maxBlock($start, 32);
		$x = log($end - $start + 1) / log(2);
		$maxDiff = floor(32 - floor($x));

		$ip = long2ip($start);

		if ($maxSize < $maxDiff) {
			$maxSize = $maxDiff;
		}

		array_push($result, "$ip/$maxSize");
		$start += pow(2, (32 - $maxSize));
	}

	return $result;
}

function ipv6ToCidr($range)
{
	$split_char = '';
	// 获取分割字符
	if (stristr($range, '-') !== false)
		$split_char = '-';
	elseif (stristr($range, ',') !== false)
		$split_char = ',';
	elseif (stristr($range, '|') !== false)
		$split_char = '|';
	else {
		throw new \Exception("Can't find Split Char\n", 1);
	}

	list($ipFrom, $ipTo) = explode($split_char, $range);
	$start_ip = trim($ipFrom);
	$end_ip = trim($ipTo);

	$ipFromBinary = str_pad(ip2Bin($ipFrom), 128, '0', STR_PAD_LEFT);
	$ipToBinary = str_pad(ip2Bin($ipTo), 128, '0', STR_PAD_LEFT);

	if ($ipFromBinary === $ipToBinary) {
		return [$ipFrom . '/' . 128];
	}

	if (strcmp($ipFromBinary, $ipToBinary) > 0) {
		list($ipFromBinary, $ipToBinary) = [$ipToBinary, $ipFromBinary];
	}

	$networks = [];
	$networkSize = 0;

	do {
		if (substr($ipFromBinary, -1, 1) == '1') {
			$networks[substr($ipFromBinary, $networkSize, 128 - $networkSize) . str_repeat('0', $networkSize)] = 128 - $networkSize;

			$n = strrpos($ipFromBinary, '0');
			$ipFromBinary = (($n == 0) ? '' : substr($ipFromBinary, 0, $n)) . '1' . str_repeat('0', 128 - $n - 1);
		}

		if (substr($ipToBinary, -1, 1) == '0') {
			$networks[substr($ipToBinary, $networkSize, 128 - $networkSize) . str_repeat('0', $networkSize)] = 128 - $networkSize;
			$n = strrpos($ipToBinary, '1');
			$ipToBinary = (($n == 0) ? '' : substr($ipToBinary, 0, $n)) . '0' . str_repeat('1', 128 - $n - 1);
		}

		if (strcmp($ipToBinary, $ipFromBinary) < 0) {
			continue;
		}

		$shift = 128 - max(strrpos($ipFromBinary, '0'), strrpos($ipToBinary, '1'));
		$ipFromBinary = str_repeat('0', $shift) . substr($ipFromBinary, 0, 128 - $shift);
		$ipToBinary = str_repeat('0', $shift) . substr($ipToBinary, 0, 128 - $shift);
		$networkSize += $shift;
		if ($ipFromBinary === $ipToBinary) {
			$networks[substr($ipFromBinary, $networkSize, 128 - $networkSize) . str_repeat('0', $networkSize)] = 128 - $networkSize;
			continue;
		}
	} while (strcmp($ipFromBinary, $ipToBinary) < 0);

	ksort($networks, SORT_STRING);
	$result = [];

	foreach ($networks as $ip => $netmask) {
		$result[] = bin2Ip($ip) . '/' . $netmask;
	}

	return $result;
}

function cidrToIpv4($cidr)
{
	if (strpos($cidr, '/') === false) {
		return;
	}

	list($ip, $prefix) = explode('/', $cidr);

	$ipStart = long2ip((ip2long($ip)) & ((-1 << (32 - (int) $prefix))));

	$total = 1 << (32 - $prefix);

	$ipStartLong = sprintf('%u', ip2long($ipStart));
	$ipEndLong = $ipStartLong + $total - 1;

	if ($ipEndLong > 4294967295) {
		$ipEndLong = 4294967295;
	}

	$ipLast = long2ip($ipEndLong);

	return [
		'ip_start' => $ipStart,
		'ip_end'   => $ipLast,
	];
}

function cidrToIpv6($cidr)
{
	if (strpos($cidr, '/') === false) {
		return;
	}

	list($ip, $range) = explode('/', $cidr);

	// Convert the IPv6 into binary
	$binFirstAddress = inet_pton($ip);

	// Convert the binary string to a string with hexadecimal characters
	$hexStartAddress = @reset(@unpack('H*0', $binFirstAddress));

	// Get available bits
	$bits = 128 - $range;

	$hexLastAddress = $hexStartAddress;

	$pos = 31;
	while ($bits > 0) {
		// Convert current character to an integer
		$int = hexdec(substr($hexLastAddress, $pos, 1));

		// Convert it back to a hexadecimal character
		$new = dechex($int | (pow(2, min(4, $bits)) - 1));

		// And put that character back in the string
		$hexLastAddress = substr_replace($hexLastAddress, $new, $pos, 1);

		$bits -= 4;
		--$pos;
	}

	$binLastAddress = pack('H*', $hexLastAddress);

	return [
		'ip_start' => expand(inet_ntop($binFirstAddress)),
		'ip_end'   => expand(inet_ntop($binLastAddress)),
	];
}

function bin2Ip($bin)
{
	if (\strlen($bin) != 128) {
		return;
	}

	$pad = 128 - \strlen($bin);
	for ($i = 1; $i <= $pad; ++$i) {
		$bin = '0' . $bin;
	}

	$bits = 0;
	$ipv6 = '';

	while ($bits <= 7) {
		$bin_part = substr($bin, ($bits * 16), 16);
		$ipv6 .= dechex(bindec($bin_part)) . ':';
		++$bits;
	}

	return inet_ntop(inet_pton(substr($ipv6, 0, -1)));
}

function compressIpv6($ipv6)
{
	return inet_ntop(inet_pton($ipv6));
}

function expandIpv6($ipv6)
{
	$hex = unpack('H*0', inet_pton($ipv6));

	return implode(':', str_split($hex[0], 4));
}

function ip2Bin($ip)
{
	if (($n = inet_pton($ip)) === false) {
		return false;
	}

	$bits = 15;
	$binary = '';
	while ($bits >= 0) {
		$bin = sprintf('%08b', (\ord($n[$bits])));
		$binary = $bin . $binary;
		--$bits;
	}

	return $binary;
}

function maxBlock($base, $bit)
{
	while ($bit > 0) {
		$decimal = hexdec(base_convert((pow(2, 32) - pow(2, (32 - ($bit - 1)))), 10, 16));

		if (($base & $decimal) != $base) {
			break;
		}

		--$bit;
	}

	return $bit;
}

function expand($ipv6)
{
	return implode(':', str_split(unpack('H*0', inet_pton($ipv6))[0], 4));
}
