<?php
//全局配置
//为了配置的简单，只支持一维数组，在应用程序中的
//常量定义
define('DS', DIRECTORY_SEPARATOR);

$config=array(
	//日志缓冲区大小
	'log_bufferSize'=>1024,
	//框架运行日志 true开启，非true关闭
	'log_enable'=>true,
	//日志最大容量 50M
	'log_maxSize'=>50*1024*1024,
	'log_name'=>'app.log',//log的名字
	'log_tagFilter'=>array(//标签过滤，不显示以下的标签
		//'core',
	),
	

	//权限控制 用户详细信息
	'rbac_userSessionKey'=>'ep_rbac_user',
	//角色
	'rbac_roleSessionKey'=>'ep_rbac_role',
	//是否启用 rbac
	'rbac_enable'=>true,
	//默认权限:all 
	'rbac_default'=>'.*',
	//身份认证失败页面
	'rbac_failed_page'=>'',
	
	//route engine
	'route_enable'=>true,
	//默认路由,将合并到配置路由的最后一行中
	'route_default'=>array(
		'rule'=>'/(.*?)/(.*?)/{0,1}',
		'controller'=>1,//如果不为数字，那么指定一个默认值
		'action'=>2,
		'args'=>array(),//参数
	),
	
	//404自定义页面,有四种not found
	//if empty then ignore,and only can see what happen in log.
	'app_not_found'=>'404',
	'controller_not_found'=>'404',
	'action_not_found'=>'404',
	'view_not_found'=>'',
	
	//
	'charset'=>'utf-8',
	//以下请勿覆盖-------
	
	//框架目录,默认是在项目目录的lib里面,如有需要可以更改
	'lib_dir'=>dirname(__FILE__).DS.'lib',
	//项目目录,和 globalconfig.php 在同一个目录
	'project_dir'=>dirname(__FILE__),

);

//load main entrance
require_once($config['lib_dir'].DS.'main.php');
//instance main object E
E::instance($config);