<?php 
//������404ʱ���ж��ǲ������404
//�������һ��ȫ��·�� ,ʹ�ÿ��Խ���Щ
if(true){
	header('HTTP/1.1 200 OK');
	echo '200';
}else{
	header('HTTP/1.0 404 Not Found');
	echo nl2br(var_export($_SERVER,true));
}
?>
