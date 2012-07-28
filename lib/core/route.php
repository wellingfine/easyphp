<?php
//url rote
// /index.php/a/b =>pathinfo -> /a/b
//$_SERVER['PATH_INFO']
class EP_Route{
	
	
	//从 path info 中获取 ctrlname 和 actname
	public static function dispatch(&$ctrlName,&$actName,$rules){
		$path_info = '/';
		$path_info = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : $path_info;
		$path_info = isset($_SERVER['ORIG_PATH_INFO']) ? $_SERVER['ORIG_PATH_INFO'] : $path_info;
		foreach($rules as $rule=>$dest){
			// replace / to \/
			$rule='/'.preg_replace('/\//','/\\\//',$rule);
			if($rule==$path_info){
				
			}
		}
		E::log($_SERVER);
		//$_GET['']
	}
}
