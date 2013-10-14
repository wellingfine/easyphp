<?php
//全局配置
//为了配置的简单，只支持一维数组，在应用程序中的
//常量定义
define('DS', DIRECTORY_SEPARATOR);

//use by framework will start with '_'
$config=array(
	//日志缓冲区大小
	'_log_bufferSize'=>1024,
	//框架运行日志 true开启，非true关闭
	'_log_enable'=>true,
	//日志最大容量 50M
	'_log_maxSize'=>50*1024*1024,
	'_log_name'=>'app.log',//log的名字
	'_log_tagFilter'=>array(//标签过滤，不显示以下的标签
		//'core',
	),
	

	//权限控制 用户详细信息
	'_rbac_userSessionKey'=>'ep_rbac_user',
	//角色
	'_rbac_roleSessionKey'=>'ep_rbac_role',
	//是否启用 rbac
	'_rbac_enable'=>true,
	//默认权限:all=>.*   limit=>.+
	'_rbac_default'=>'.+',
	//身份认证失败页面,jump to view
	'_rbac_failed_page'=>'403',
	
	//route engine
	'_route_enable'=>true,
	//默认路由,将合并到配置路由的最后一行中
	'_route_default'=>array(
		'rule'=>'/(.*?)/(.*?)/{0,1}',
		'controller'=>1,//如果不为数字，那么指定一个默认值
		'action'=>2,
		'args'=>array(),//参数
	),
	
	//four types of NotFound
	//if empty then ignore,but you can see what happen in log.
	'_controller_not_found'=>'404',
	'_action_not_found'=>'404',
	'_view_not_found'=>'',
	
	//
	'_charset'=>'utf-8',
	//以下请勿覆盖-------
	//when needed ,framework will load dbconfig(db.php) to cover this value even defined in subconfig, 
	'_db_config'=>null,
	//框架目录,默认是在项目目录的lib里面,如有需要可以更改
	'_lib_path'=>dirname(__FILE__).DS.'lib'.DS,
	//项目目录,和 globalconfig.php 在同一个目录
	'_project_path'=>dirname(__FILE__).DS,
	'_app_path'=>'',
	'_session_life_time'=>432000,

	//runtime value ---
	'_app_path'=>'',
	'_app_name'=>'',
	
	//current controller name and action name
	'_controller'=>'',
	'_action'=>'',
);

header('Content-Type: text/html;charset='.$config['_charset']);
//load main entrance
require_once($config['_lib_path'].'main.php');
//create main object E
E::createMe($config);
