<?php
setcookie("user_name", "", time()-2592000,'/');
setcookie("pass_word", "", time()-2592000,'/');
print_r($_COOKIE);
?>