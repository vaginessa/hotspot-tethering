<?php
clearstatcache();
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

$binary = sys_get_temp_dir()."/aria2c";

if(!is_executable($binary) or file_exists('aria2c')) {
//rename('./binary', $binary);
copy('aria2c', $binary);
chmod($binary, 0700);
}

//$dir = $_SERVER['DOCUMENT_ROOT']?str_replace('\\','/',$_SERVER['DOCUMENT_ROOT']):str_replace('\\','/',dirname(__FILE__));
$dir = dirname(__FILE__);

$run = $binary.' --conf-path='.$dir.'/aria2.conf --dir='.$dir.'/Download --log='.$dir.'/aria2.log --input-file='.$dir.'/aria2.session --save-session='.$dir.'/aria2.session --save-cookies='.$dir.'/Cookie --load-cookies='.$dir.'/Cookie --dht-file-path='.$dir.'/dht.dat';

if (stripos(shell_exec('ps -A'), 'aria2c') === false) {
    shell_exec($run);
}


header("Location: ./AriaNg/");   
?>