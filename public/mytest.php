<?php

//װ��ȫ������
$config=require_once('../globalconfig.php');
//��ʼ�ַ�����
E::instance()->start(basename(__FILE__,".php"));


print_r(' <hr><br>');
ob_start();
print_r(E::$config);
echo nl2br(ob_get_clean());
?> 