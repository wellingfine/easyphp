<?php
//列举所有核心类然后
$class=array(
	'core/controller.php',
	'core/log.php',
	'core/view.php',
	
);

foreach($class as $c){
	require_once($c);
}