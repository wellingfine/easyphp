<?php
//�о����к�����Ȼ��
$class=array(
	'core/controller.php',
	'core/log.php',
	'core/view.php',
	
);

foreach($class as $c){
	require_once($c);
}