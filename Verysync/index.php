<?php
require '../Admin/main.class.php';
$receive = htmlspecialchars($_POST['receive']);
$pkill = toolbox_check() [1] . ' pkill';
if (!file_exists('verysync')) {
    die("{\"a\": \"程序主文件不见了！\",\"b\": 1}");
}
if (!file_exists('tmp')) {
    mkdir('tmp');
}
$binary_file = sys_get_temp_dir() . '/verysync';
if (!is_executable($binary_file) && file_exists('verysync')) {
    copy('verysync', $binary_file);
    chmod($binary_file, 0700);
}
function shell_input($run,$p) { 
    exec($run, $output, $return_val);
    sleep(2);
    if ($return_val != 0) {
        die("{\"a\": \"$p 失败！返回值: $return_val\",\"b\": 1}");
    } else { 
        die("{\"a\": \"$p 成功\",\"b\": 0}");
    }
}
if (isset($receive) and $receive == 'start') {
  $run = 'export HOME=' . __DIR__ . PHP_EOL . $binary_file . ' -home ' . __DIR__ . '/tmp -gui-address :8886 -no-browser > /dev/null &';
  shell_input($run,'微力同步启动');
}
if (isset($receive) and $receive == 'stop') {
  $run = $pkill . ' verysync';
  shell_input($run,'微力同步停止');
}
?>