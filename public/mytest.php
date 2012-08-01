<?php

//装载全局配置
$config=require_once('../globalconfig.php');
//开始分发请求
E::instance()->start(basename(__FILE__,".php"));


print_r(' <hr><br>');
ob_start();
print_r(E::$config);
echo nl2br(ob_get_clean());
?> 