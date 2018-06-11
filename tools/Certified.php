<?php
if (file_exists('../Admin/admin.php')) { 
require '../Admin/admin.php';
}
if ($_COOKIE["user_name"] != U || $_COOKIE["pass_word"] != P) { 
header("Location: ../Admin");
die("需要登录认证才能访问!");
}
?>