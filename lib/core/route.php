<?php
//url rote
// /index.php/a/b =>pathinfo -> /a/b
//$_SERVER['PATH_INFO']

/*
	route.config
	
	/app/sql/12345
	array(
		'rule'=>'/sql/([0-9]+)',
		'controller'=>'dispatcher',
		'action'=>'callsql',
		'args'=>array(
			'id'=>1, //=> pattern group index
			'format'=>2,
		),

		'prefix'=>'url_' // if given ,EP will add $_GET['url_'.'id']=$val ,otherwise $_GET['id']=$val 
	),

*/
class EP_Route{
	
	
	//从 path info 中获取 ctrlname 和 actname
	public static function dispatch(&$ctrlName,&$actName){

		$rules=include(E::c('_app_path').'route.php');
		
		$pathInfo = '/';
		$pathInfo = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : $pathInfo;
		//如有orig_path_info 则使用这个，有时path_info会出错
		$pathInfo = isset($_SERVER['ORIG_PATH_INFO']) ? $_SERVER['ORIG_PATH_INFO'] : $pathInfo;
		//ignore the last '/'
		$pathInfo = rtrim($pathInfo,'/');

		//最后的地址会像这样  $pathInfo='/control//action'
		E::log('route pathinfo:'.$pathInfo,'core');
		if(empty($pathInfo))return ;
		//设置Pathinfo的值 
		$arr=preg_split('/\/+/',$pathInfo);
		E::c('_path_info',$arr);

		//遍历 路由规则
		foreach($rules as $rule){
			$prefix=E::get('prefix','',$rule);
			$reg=$rule['rule'];
			// replace / to \/
			$reg='/^'.preg_replace('/\//','\\/',$reg).'$/';
			//E::log('route reg:'.$reg);
			if(preg_match($reg,$pathInfo,$matches)){
				$ctrlName=is_int( $rule['controller'] )?
					(E::get($rule['controller'],'',$matches)):
					$rule['controller'];
				$actName=is_int( $rule['action'] )?
					(E::get($rule['action'],'',$matches)):
					$rule['action'];
				
				if(isset($rule['args'])){
					//assign args to $_GET and if REQUEST isn't set assgin to it too(avoid same arg in $_POST)
					foreach($rule['args'] as $name=>$index){
						$name=$prefix.$name;
						$_GET[$name]=E::get($index,'',$matches);
						if(!isset($_REQUEST[$name])){
							$_REQUEST[$name]=$_GET[$name];
						}
					}
				}
				return ;
			}
		}
		//如果没有匹配到
		
		$ctrlName=E::get(1,$ctrlName,$arr);
		$actName=E::get(2,$actName,$arr);
		
	}
	
}
