<?php
if (file_exists('../Admin/admin.php')) { 
require '../Admin/admin.php';
} else {
die('管理员密码配置文件遗失');
}
if ($_COOKIE['user_name'] != hash('sha512',U) || $_COOKIE['pass_word'] != hash('sha512',P)) { 
header('Location: ../Admin');
die('需要登录认证才能访问!');
}
?>