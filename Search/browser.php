<!DOCTYPE html>
<html>
 <head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0, initial-scale=1.0, user-scalable=no" />
  <meta name="format-detection" content="telephone=no, email=no" />
  <meta name="HandheldFriendly" content="true" />
  <meta http-equiv="cache-control" content="max-age=0" />
  <meta http-equiv="cache-control" content="no-cache" />
  <meta http-equiv="expires" content="0" />
  <meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT" />
  <meta http-equiv="pragma" content="no-cache" />
  <title>QUI Demo</title>
  <link rel="shortcut icon" href="./favicon.ico" />
  <link rel="bookmark" href="./favicon.ico" />
  <link rel="stylesheet" href="../css/frozenui.css" />
  <link rel="stylesheet" href="../css/style.css" />
 </head>
 <body ontouchstart="">
<?php
require_once 'config.php';
require '../Tools/curl.php';
require '../Tools/input.php';

$decodeurl = getUrlKeyValue(urldecode(base64_decode($_GET['encryption'])));


if ($_SERVER["REQUEST_METHOD"] == "GET") {
   $text = test_input($decodeurl['text']);
   $safe = test_input($decodeurl['safe']);
   $zhongzhuan = test_input($decodeurl['zhongzhuan']);
   $luesuo = test_input($decodeurl['luesuo']);
   $start = test_input($decodeurl['start']);   
}

if (empty($text) or empty($safe)) {
die("请不要乱修改URL参数。谢谢！<br><a href='./'>返回首页</a></body></html>");
}

if (empty($start)) $start = 1;

//拼接搜索引擎需要的url
//因为text可能含有中文和空格，前面所有都解码了现在重新url编码text
$newurl = $URL.urlencode($text)."&safe=".$safe."&start=".$start."&num=".$NUM."&cx=".$ID."&key=".$APIKEY;
//print_r($newurl);

//获取数据
$data = GET($newurl); 

if ($data)
{
$obj = json_decode($data);
$err = $obj->error->code;
$title = $obj->context->title;
$searchTerms = $obj->queries->request[0]->searchTerms;
$searchTime = $obj->searchInformation->searchTime;
$totalResults = $obj->searchInformation->totalResults;
$safe = $obj->queries->request[0]->safe;
$startIndex = $obj->queries->request[0]->startIndex;
$count = $obj->queries->request[0]->count;
$startIndexs = $obj->queries->nextPage[0]->startIndex;
}
else
{
echo <<<EOF
                <p class="ui-txt-warning">可能没有得到搜索引擎的反馈！或是服务器访问失败了。</p><br><a href='./'>返回首页</a></body></html>
EOF;
exit();
}

if ($err)
{
die("错误代码: ".$err."<br>错误信息: ".$obj->error->message."<br><a href='./'>返回首页</a></body></html>");
}

if (empty($totalResults))
{
echo <<<EOF
                <p class="ui-txt-warning">没有找到搜索结果！</p>
                <br>
                <a href='./'>返回首页</a>
                </body>
                </html>
EOF;
exit();
}
else
{
echo <<<EOF
<ul class="ui-list  ui-border-tb ui-list-nospace">
EOF;
echo "<script type='text/javascript'>document.title = '".$searchTerms."_".$title."';</script>";

    foreach($obj->items as $v){    
    if ($luesuo == "true")
    {
    $luesuo2 = $v->pagemap->cse_thumbnail[0]->src;
    } else {
    $luesuo2 = $v->pagemap->cse_image[0]->src;
    }    
    if ($zhongzhuan == "true")
    {
    $zhongzhuan2 = "./image.php?imgurl=".$luesuo2;
    } else {
    $zhongzhuan2 = $luesuo2;
    }
    echo "<li><div class='ui-list-img-horizontal'><span style='background-image:url(".$zhongzhuan2.")'></span></div><div class='ui-list-info'><h4 class='ui-nowrap'><a href='".$v->link."' target='_blank'>".$v->htmlTitle."</a></h4><p class='ui-nowrap'>".$v->htmlFormattedUrl."<br>".$v->htmlSnippet."</div></li>";
    
  }
    if ($zhongzhuan)
    {
    $zhongzhuan = "&zhongzhuan=".$zhongzhuan;
    }
    if ($luesuo)
    {
    $luesuo = "&luesuo=".$luesuo;
    }
    $rand = "rand=".rand();
    //拼接服务器需要的url
    //因为我发现二次url编码好像没什么改变所以不用再解码，不用再次修改。
    $newurl = "?encryption=".base64_encode(urlencode($rand."&start=".$startIndexs."&text=".$text.$zhongzhuan.$luesuo."&safe=".$safe));
    echo "</ul>";
    echo "<div class='ui-btn-wrap'>
                <button class='ui-btn-lg' onclick="."window.location.replace('".$newurl."')"." >
                    下一页
                </button>
                </div>";
}

?>

</body>
</html>