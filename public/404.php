<?php 
//������404ʱ���ж��ǲ������404
//�������һ��ȫ��·�� ,ʹ�ÿ��Խ���Щ
if(true){
	header('HTTP/1.1 200 OK');
	header('Location: /index'.$_SERVER['REQUEST_URI']);

/*	
	echo '200<br>';
	echo nl2br(var_export($_SERVER,true));
	return ;
	//װ��ȫ������
	$config=require_once('../globalconfig.php');
	//��ʼ�ַ�����
	E::instance()->start('index');
*/
}else{
	header('HTTP/1.0 404 Not Found');
	echo '404<br>';
	echo nl2br(var_export($_SERVER,true));
}
?>
