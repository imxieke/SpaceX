<?php
ini_set('memory_limit','1024M');

$qqwry = dirname(__DIR__) . '/databases/qqwry.dat';
$csv = dirname(__DIR__) . '/databases/qqwry.csv';
$db11 = dirname(__DIR__) . '/databases/build/ip2location-lite-db11.txt';
require_once __DIR__ . '/qqwry.php';
$qw = new Pureip($qqwry);

$db11 = file($db11);
unset($db11[0]);
$db11_total = count($db11);
foreach($db11 as $key => $ipinfo)
{
	echo "Now is " . $key . ' 剩余 ' . ($db11_total - $key). PHP_EOL;
	// format db11 start end qqwry start end country area
	$ipinfo = explode(',', $ipinfo);
	$ip = $ipinfo[0];
	$data = $qw->get($ip);
	$endip = $ipinfo[1];
	$str = $ip.','.$endip.','.$data['beginip'].','.$data['endip'].','.$data['country'].','.$data['area'] .PHP_EOL;
	file_put_contents($csv, $str,FILE_APPEND);
	// print_r($data);
	// print_r($str);
	// print_r($ipinfo);
}

// print_r($data);
// print_r($qw);
