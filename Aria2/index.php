<?php
require "../Shadowsocks/busybox.php";
$ps=busybox_check("ps");
if(!is_file('./aria2c')) {
die('程序主文件不见了');
}

if(!is_file('./aria2.conf')) {
die('程序配置文件不见了');
}

if(!is_dir('./Download')) {
mkdir('./Download');
}

if(!is_file('./aria2.log')) {
touch('./aria2.log');
}

if(!is_file('./aria2.session')) {
touch('./aria2.session');
}

if(!is_file('./Cookie')) {
touch('./Cookie');
}

$Binary = sys_get_temp_dir()."/aria2c";

if(!is_file($Binary)) {
//rename('./Binary', $Binary);
copy('aria2c', $Binary);
chmod($Binary, 0700);
}

//$Dir = $_SERVER['DOCUMENT_ROOT']?str_replace('\\','/',$_SERVER['DOCUMENT_ROOT']):str_replace('\\','/',dirname(__FILE__));
$Dir = dirname(__FILE__);

$run = $Binary.' --conf-path='.$Dir.'/aria2.conf --dir='.$Dir.'/Download --log='.$Dir.'/aria2.log --input-file='.$Dir.'/aria2.session --save-session='.$Dir.'/aria2.session --save-cookies='.$Dir.'/Cookie --load-cookies='.$Dir.'/Cookie --dht-file-path='.$Dir.'/dht.dat';

if (stripos(shell_exec('ps -A'), 'aria2c') === false) {
    shell_exec($run);
}


header("Location: ./AriaNg/");   
?>