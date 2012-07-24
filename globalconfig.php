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
	
	/*
		框架本身的acl(Access Control List) 只能支持单角色
		先判deny后allow
		如果deny为空，表示不deny任何人
		如果allow为空，表示不allow任何人
	*/
	//权限控制 用户详细信息
	'acl_userSessionKey'=>'ep_acl_user',
	//角色
	'acl_roleSessionKey'=>'ep_acl_role',
	//是否启用 acl
	'acl_enable'=>true,
	//默认权限
	'acl_default'=>'all',
	
	//以下请勿覆盖-------
	
	//框架目录,默认是在项目目录的lib里面,如有需要可以更改
	'lib_dir'=>dirname(__FILE__).DS.'lib',
	//项目目录,和 globalconfig.php 在同一个目录
	'project_dir'=>dirname(__FILE__),

);

//装载框架主类
require_once($config['lib_dir'].DS.'main.php');
//初始化主类
E::instance($config);
