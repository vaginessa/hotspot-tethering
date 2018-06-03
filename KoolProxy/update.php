<?php
error_reporting(0);
clearstatcache();
date_default_timezone_set("Asia/Shanghai");
$date = date("Y-m-d H:i:s");
if (!isset($_POST["rand"])) die("拒绝访问！$date");
function curlGet($download_url) {
    $ssl = substr($surl, 0, 8) == "https://" ? TRUE : FALSE;
    $ch = curl_init();
    $opt = array(
        CURLOPT_URL => $download_url,
        CURLOPT_USERAGENT => $_SERVER["HTTP_USER_AGENT"],
        CURLOPT_COOKIE => "",
        CURLOPT_HEADER => false,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_FOLLOWLOCATION => true,
    );
    if ($ssl) {
        $opt[CURLOPT_SSL_VERIFYHOST] = 2;
        $opt[CURLOPT_SSL_VERIFYPEER] = false;
    }
    curl_setopt_array($ch, $opt);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}
$file = array(
    "kp.dat",
    "daily.txt",
    "koolproxy.txt"
);
$file_dir = "rules/";
$download_url = "https://kprule.com/";
echo "上次检查更新时间: " . file_get_contents($file_dir . "update.log") . "\n";
foreach ($file as $value) {
    if (!is_file($file_dir . $value)) {
        touch($file_dir . $value);
    }
    $a = sha1_file($file_dir . $value);
    $data = curlGet(urldecode($download_url . $value));
    if ($data) {
        file_put_contents($value, $data, LOCK_EX);
        $b = sha1_file($value);
        if ($a != $b) {
            rename($value, $file_dir . $value);
            echo $value . " 更新完成！\n";
            if ($value != "kp.dat") {
                echo "有效规则 " . (sizeof(file($file_dir . $value)) - 9) . " 条\n";
            }
        } else {
            echo $value . " 无需更新！\n";
            unlink($value);
        }
    } else {
        die("下载 " . $value . " 时失败，请检查网络！\n");
    }
} //
file_put_contents($file_dir . "update.log", $date, LOCK_EX);
?>