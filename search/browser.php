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
require_once 'waf.php';
$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : NULL;
$host = $_SERVER['HTTP_HOST'];
/*
echo '提交过来的地址：'.$referer;
echo '<br>';
echo '本站域名：'.$host;
echo '<br>';
echo substr($referer,7,strlen($host));
*/
if(substr($referer,7,strlen($host)) != $host){
 echo '非法操作';
}
/*else{
 echo '正常操作';
}
*/

/*
// 代理协议  CURLPROXY_HTTP (默认值，代理为 HTTP、HTTPS 都设置此值)、 CURLPROXY_SOCKS4、 CURLPROXY_SOCKS5、 CURLPROXY_SOCKS4A、CURLPROXY_SOCKS5_HOSTNAME
curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);

// 代理地址
curl_setopt($ch, CURLOPT_PROXY, $strProxyServer);

// 代理端口号，也可以写在代理地址里面
curl_setopt($ch, CURLOPT_PROXYPORT, $strProxyPort);

// 代理的用户名和密码
curl_setopt($ch, CURLOPT_PROXYUSERPWD, "$strProxyUser:$strProxyPassWord");
*/

function getUrlKeyValue($url)
{
    $result = array();
    $mr = preg_match_all('/(\?|&)(.+?)=([^&?]*)/i', $url, $matchs);
    if ($mr !== false) {
        for ($i = 0; $i < $mr; $i++) {
            $result[$matchs[2][$i]] = $matchs[3][$i];
        }
    }
    return $result;
}

function test_input($data) {
   $data = trim($data);
   $data = stripslashes($data);
   $data = htmlspecialchars($data);
   return $data;
}

function curlGet($surl)  
{  
    $ssl = substr($surl, 0, 8) == "https://" ? TRUE : FALSE;  
    $ch = curl_init();  
    $opt = array(  
            CURLOPT_URL     => $surl,  
            CURLOPT_USERAGENT    => $_SERVER ['HTTP_USER_AGENT'],  
            CURLOPT_COOKIE  => '',
            CURLOPT_HEADER  => false,  
            CURLOPT_RETURNTRANSFER  => true,  
            CURLOPT_TIMEOUT => 30,  
            );  
    if ($ssl)  
    {  
        $opt[CURLOPT_SSL_VERIFYHOST] = 1;  
        $opt[CURLOPT_SSL_VERIFYPEER] = FALSE;  
    }  
    curl_setopt_array($ch, $opt);  
    $data = curl_exec($ch);  
    curl_close($ch);
    return $data;
}

$decodeurl = getUrlKeyValue(urldecode(base64_decode($_GET['encryption'])));


if ($_SERVER["REQUEST_METHOD"] == "GET") {
   $text = test_input($decodeurl['text']);
   $safe = test_input($decodeurl['safe']);
   $zhongzhuan = test_input($decodeurl['zhongzhuan']);
   $luesuo = test_input($decodeurl['luesuo']);
   $start = test_input($decodeurl['start']);   
}

if (empty($text or $safe))
{
die("请不要乱修改URL参数。谢谢！<br><a href='./'>返回首页</a></body></html>");
}

if (empty($start))
{
$start = 1;
}

//拼接搜索引擎需要的url
//因为text可能含有中文和空格，前面所有都解码了现在重新url编码text
$newurl = $URL.urlencode($text)."&safe=".$safe."&start=".$start."&num=".$NUM."&cx=".$ID."&key=".$APIKEY;


//获取数据
$data = curlGet($newurl); 

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
/*
echo ($searchTerms." - ".$title."<br>找到约 ".$totalResults." 条结果 (用时: ".$searchTime." 秒)  安全搜索: ".$safe."<br>当前页: ".$startIndex." (每页获取 ".$count." 条搜索结果)  下一页: ".$startIndexs);
echo"<br><br>";
*/

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
    
    /*
       echo "<img src='image.php?imgurl=".$v->pagemap->cse_thumbnail[0]->src."' alt='图片抓取失败'>";
       echo "<a href='".$v->link."'>".$v->htmlTitle."</a><br>";
       echo $v->htmlFormattedUrl."<br>";
       echo $v->htmlSnippet."<hr>";
       */
       
       /*
       echo $v->htmlTitle."<br>";
       echo $v->htmlFormattedUrl."<br>";
       echo $v->htmlSnippet."<br>";
       echo $v->link."<br>";
       echo $v->pagemap->cse_thumbnail[0]->src."<br>";
       echo $v->pagemap->cse_image[0]->src."<br>";
       */
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