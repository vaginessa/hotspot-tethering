<?php
if (empty($_SERVER['HTTP_REFERER'])){
die('对不起，不允许从地址栏访问');
}

$imgurl = $_GET['imgurl'];

if (stripos($imgurl, "http") !== false)
{
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $imgurl);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_USERAGENT, "APIs-Google (+https://developers.google.com/webmasters/APIs-Google.html)");
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
curl_setopt($ch, CURLOPT_REFERER, "http://www.google.com");
$img = curl_exec($ch);  
curl_close($ch);
}

if (empty($img))
{
//设置失败图片显示
$imgurl = '../img/No_image_available.svg.png';
//获得图片
$img = file_get_contents($imgurl,true);
}
//end 取数组 getimagesize直接设置
header("Content-Type:".end(getimagesize($imgurl)));
//显示图片
echo $img;
?>