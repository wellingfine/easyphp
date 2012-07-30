<?php 
//当出现404时，判断是不是真的404
echo '404<br>';
echo nl2br(var_export($_SERVER,true));
return ;
if(true){
	header('HTTP/1.1 200 OK');
	header('Location: /index'.$_SERVER['REQUEST_URI']);

/*	
	echo '200<br>';
	echo nl2br(var_export($_SERVER,true));
	return ;
	//装载全局配置
	$config=require_once('../globalconfig.php');
	//开始分发请求
	E::instance()->start('index');
*/
}else{
	header('HTTP/1.0 404 Not Found');
	echo '404<br>';
	echo nl2br(var_export($_SERVER,true));
}
?>
