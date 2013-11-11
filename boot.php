<?php
/*
	start config.
*/

define('DS', DIRECTORY_SEPARATOR);
$dir=dirname(__FILE__).DS;

//all config
$config=array(

	//
	'_log_bufferSize'=>1024, //once full will log down to file.
	'_log_enable'=>true,
	'_log_name'=>'app',// => name.yyyymmdd.log
	'_log_tagFilter'=>array(//hide these tag's log
		//'core',//framework's tag
		//'db',//db operation
	),
	
	//权限控制 用户详细信息
	'_rbac_userSessionKey'=>'ep_rbac_user',
	//角色
	'_rbac_roleSessionKey'=>'ep_rbac_role',
	//是否启用 rbac
	'_rbac_enable'=>true,
	//默认权限:all=>.*   limit=>.+
	'_rbac_default'=>'.*',
	//身份认证失败页面,jump to view
	'_rbac_failed_page'=>'403',
	
	//route engine
	'_route_enable'=>true,
	
	//four types of NotFound
	//if empty then ignore,but you can see what happen in log.
	'_controller_not_found'=>'404',
	'_action_not_found'=>'404',
	'_view_not_found'=>'',
	
	//环境变量
	'_charset'=>'utf-8',
	'_session_life_time'=>432000,
	'_cache_time'=>36000, //默认缓存时间


	//path setting .
	'_lib_path'=>$dir.'lib'.DS,
	'_app_path'=>$dir.'app'.DS,
	'_cache_path'=>$dir.'app'.DS.'tmp'.DS.'cache'.DS,
	'_public_path'=>$dir.'public'.DS,

	/*
		runtime values, framework will set these value when running,
		so don't change it in any case.
	*/
	'_controller'=>'',
	'_action'=>'',
	'_db_config'=>null,
	'_path_info'=>'',
);

header('Content-Type: text/html;charset='.$config['_charset']);
//load main entrance
require($config['_lib_path'].'main.php');
//create main object E
E::createMe($config);
E::i()->start();
