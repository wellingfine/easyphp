<?php


$my=array(
	array(
		'name'=>'my',
		'a'=>'a'
	),
	array(
		'name2'=>'my2',
		'a2'=>'a2'
	),
);
print_r($my);
echo "\n";
$end=&end($my);
print_r($end);
$end['name2']='aaaaa';

echo "\n";
print_r($my);