<?php
require '../Admin/main.class.php';
date_default_timezone_set('Asia/Shanghai');
$date = date('Y-m-d H:i:s');
$file = array(
    'kp.dat',
    'daily.txt',
    'koolproxy.txt'
);
$file_dir = 'rules/';
$download_url = 'http://kprule.com/';
echo '上次检查更新时间: ' . file_get_contents($file_dir . 'update.log') . "\n";
foreach ($file as $value) {
    if (!file_exists("$file_dir$value")) {
        touch("$file_dir$value");
    }
    $oldfile = sha1_file("$file_dir$value");
    $data = GET(urldecode("$download_url$value"));
    if ($data) {
        file_put_contents($value, $data);
        $newfile = sha1_file($value);
        if ($oldfile != $newfile) {
            rename($value, "$file_dir$value");
            echo "$value 更新完成！\n";
            if ($value != 'kp.dat') {
                echo '有效规则 ' . (sizeof(file("$file_dir$value")) - 9) . " 条\n";
            }
        } else {
            echo $value . " 无需更新！\n";
            unlink($value);
        }
    } else {
        die('下载 ' . $value . " 时失败，请检查网络！\n");
    }
} //
file_put_contents($file_dir . 'update.log', $date);
?>