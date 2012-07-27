<?php 
//当出现404时，判断是不是真的404
//将请求过一下全局路由 ,使得可以建立些
if(true){
	header('HTTP/1.1 200 OK');
	echo '200';
}else{
	header('HTTP/1.0 404 Not Found');
	echo nl2br(var_export($_SERVER,true));
}
?>
