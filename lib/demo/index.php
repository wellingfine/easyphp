<?php
//ini_set('display_errors', 'Off');
ini_set('display_errors', 'On');
error_reporting(E_ALL & ~E_NOTICE);
//装载全局配置
$config=require_once('../globalconfig.php');
//开始分发请求
E::i()->start(basename(__FILE__,".php"));


?>