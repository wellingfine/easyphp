<?php
//list all core classes and require
$class=array(
	'core/controller.php',
	'core/log.php',
	'core/view.php',
	'db/db.php',
	'db/table.php',
);

foreach($class as $c){
	require_once($c);
}