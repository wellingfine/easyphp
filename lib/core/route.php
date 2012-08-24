<?php
//url rote
// /index.php/a/b =>pathinfo -> /a/b
//$_SERVER['PATH_INFO']
class EP_Route{
	
	
	//从 path info 中获取 ctrlname 和 actname
	public static function dispatch(&$ctrlName,&$actName){
		$rules=require_once(E::config('app_path').'routes.php');
		
		$path_info = '/';
		$path_info = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : $path_info;
		$path_info = isset($_SERVER['ORIG_PATH_INFO']) ? $_SERVER['ORIG_PATH_INFO'] : $path_info;

		E::log('route pathinfo:'.$path_info,'core');
		//合并默认路由,到最后一行
		$rules[]=E::config('route_default');
		foreach($rules as $rule){

			$reg=$rule['rule'];
			// replace / to \/
			$reg='/^'.preg_replace('/\//','\\/',$reg).'$/';
			//E::log('route reg:'.$reg);
			if(preg_match($reg,$path_info,$matches)){
				$ctrlName=is_int( $rule['controller'] )?
					(E::get($rule['controller'],'',$matches)):
					$rule['controller'];
				$actName=is_int( $rule['action'] )?
					(E::get($rule['action'],'',$matches)):
					$rule['action'];
				
				if(isset($rule['args'])){
					//assign args to $_GET and if REQUEST isn't set assgin to it too(avoid same arg in $_POST)
					foreach($rule['args'] as $name=>$index){
						$_GET[$name]=E::get($index,'',$matches);
						if(!isset($_REQUEST[$name])){
							$_REQUEST[$name]=$_GET[$name];
						}
					}
				}
				return ;
			}
		}
	}
	
}
