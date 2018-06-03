<?php
$curl = curl_init();
$url='http://wap.baidu.com';
curl_setopt($curl, CURLOPT_URL, $url); //设置URL
curl_setopt($curl, CURLOPT_HEADER, 1); //获取Header
curl_setopt($curl,CURLOPT_NOBODY,true); //Body就不要了吧，我们只是需要Head
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); //数据存到成字符串吧，别给我直接输出到屏幕了
$data = curl_exec($curl); //开始执行啦～
echo curl_getinfo($curl,CURLINFO_HTTP_CODE); //我知道HTTPSTAT码哦～
curl_close($curl); //用完记得关掉他
?>