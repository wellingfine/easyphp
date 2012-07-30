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
		
		//合并默认路由,到最后一行
		$rules[]=E::config('route_default');
		foreach($rules as $rule){
			$r=$rule['rule'];
			//$rule=trim($rule,'/ ');
			// replace / to \/
			$r='/^'.preg_replace('/\//','\\\/',$r).'$/';
			E::log($r);
			if(preg_match($r,$path_info,$matches)){
				$ctrlName=E::get($rule['controller'],'',$matches);
				$actName=E::get($rule['action'],'',$matches);
				if(isset($rule['args'])){
					foreach($args as $name=>$id){
						//$_GET[]
					}
				}
			}
		}
		//E::log($_SERVER);
		//$_GET['']
	}
}
