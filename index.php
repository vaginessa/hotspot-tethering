<?php
session_start();
$_SESSION['from'] = 'login';
header("HTTP/1.1 302 Found");
header("Location: ./Welcom/");
session_write_close();
?>