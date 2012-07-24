<?php
/*
	默认为全开放
*/
return array(
	'default'=>array(
		'deny'=>array(),
		'allow_has_role'=>true,
		'allow'=>array(),
		'actions'=>array(
			'abc'=>array(
				'allow_has_role'=>true,
				'allow'=>array(),
				'deny'=>array(),
			)
		)
		
	),
);