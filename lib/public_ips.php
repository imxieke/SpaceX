<?php

// public self-publish ip format
// ip_range,country,region,city,postal_code

function get_all_source()
{
    $dir = __DIR__ . '/../source';
    $alibaba = 'https://regionip.aliyun.com/publish/abtn-ip2location.csv';
    if(!file_exists($dir . '/alibaba.csv'))
    {
        file_put_contents($dir . '/alibaba.csv',file_get_contents($alibaba));
    }

    $file = $dir . '/alibaba.csv';
    $lists = trim(file_get_contents($file));
    $lists = explode("\n",$lists);
    foreach ($lists as $key => $ip)
    {
        $ip = explode(',',$ip);
        print_r($ip);
        echo PHP_EOL;
        // exit;
    }
}

function github()
{
    $url = 'https://api.github.com/meta';
    echo file_get_contents($url);
}

// get_all_source();
github();