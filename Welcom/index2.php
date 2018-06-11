<html>
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0, initial-scale=1.0, user-scalable=no" />
</head>
<body>
<script src="./js/jquery-3.3.1.min.js"></script>
<script src="./js/jquery.backstretch.min.js"></script>


<?
$handle = opendir('./background/'); //当前目录
while (false !== ($file = readdir($handle))) { //遍历该php教程文件所在目录
list($filesname,$kzname)=explode(".",$file);//获取扩展名
if ($kzname=="gif" or $kzname=="jpg" or $kzname=="png") { //文件过滤
if (!is_dir('./background/'.$file)) { //文件夹过滤
$array[]=$file;//把符合条件的文件名存入数组
}
}
}
$suiji=array_rand($array); //使用array_rand函数从数组中随机抽出一个单元
?>


<script type="text/javascript">
$.backstretch("<?php echo "./background/$array[$suiji]"; ?>");
</script> 
</body>
</html>