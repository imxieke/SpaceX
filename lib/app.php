<?php 
$file = __DIR__ . '/databases/ipip_asn.json';
$data = file_get_contents($file);
$data = json_decode($data,true);

print_r($data);exit;
// print_r($ipinfodata);
$format = "code,country \n";
file_put_contents($countrys, $format);

foreach ($ipinfodata as $key => $val)
{
	$str = strtoupper($val['code']) . ',' . $val['country'] ."\n";
	file_put_contents($countrys, $str,FILE_APPEND);
}