<?php
//װ��ȫ������
$config=require_once('../globalconfig.php');
//��ʼ�ַ�����
E::instance()->start(basename(__FILE__,".php"));


print_r(' <hr><br>�ֽ���beta');
ob_start();
print_r(E::config());
echo nl2br(htmlspecialchars(ob_get_clean()));
?> 