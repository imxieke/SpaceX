<?php 

/*
 * @Author: Cloudflying
 * @Date: 2023-10-22 15:54:35
 * @LastEditTime: 2023-10-22 15:56:57
 * @LastEditors: Cloudflying
 * @Description: 
 */

// Function to convert IPs
// Returns array of CIDR notation IPs
function iprange2cidr($ipStart, $ipEnd)
{
	if (is_string($ipStart) || is_string($ipEnd)) {
		$start = ip2long($ipStart);
		$end = ip2long($ipEnd);
	} else {
		$start = $ipStart;
		$end = $ipEnd;
	}

	$result = array();

	while ($end >= $start) {
		$maxSize = 32;
		while ($maxSize > 0) {
			$mask = hexdec(iMask($maxSize - 1));
			$maskBase = $start & $mask;
			if ($maskBase != $start) {
				break;
			}
			$maxSize--;
		}
		$x = log($end - $start + 1) / log(2);
		$maxDiff = floor(32 - floor($x));

		if ($maxSize < $maxDiff) {
			$maxSize = $maxDiff;
		}

		$ip = long2ip($start);
		array_push($result, "$ip/$maxSize");
		$start += pow(2, (32 - $maxSize));
	}
	return $result;
}

function ipRange($ip, $range)
{
	if (strpos($range, '/') == false) {
		$range .= '/32';
	}

	// $range is in IP/CIDR format eg 127.0.0.1/24
	list($range, $netmask) = explode('/', $range, 2);
	$range_decimal = ip2long($range);
	$ip_decimal = ip2long($ip);
	$wildcard_decimal = pow(2, (32 - $netmask)) - 1;
	$netmask_decimal = ~$wildcard_decimal;
	return (($ip_decimal & $netmask_decimal) == ($range_decimal & $netmask_decimal));
}