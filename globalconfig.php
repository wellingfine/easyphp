<?php
//ȫ������
//Ϊ�����õļ򵥣�ֻ֧��һά���飬��Ӧ�ó����е�
//��������
define('DS', DIRECTORY_SEPARATOR);

$config=array(
	//��־��������С
	'log_bufferSize'=>1024,
	//���������־ true��������true�ر�
	'log_enable'=>true,
	//��־������� 50M
	'log_maxSize'=>50*1024*1024,
	'log_name'=>'app.log',//log������
	'log_tagFilter'=>array(//��ǩ���ˣ�����ʾ���µı�ǩ
		//'core',
	),
	
	/*
		��ܱ����acl(Access Control List) ֻ��֧�ֵ���ɫ
		����deny��allow
		���denyΪ�գ���ʾ��deny�κ���
		���allowΪ�գ���ʾ��allow�κ���
	*/
	//Ȩ�޿��� �û���ϸ��Ϣ
	'acl_userSessionKey'=>'ep_acl_user',
	//��ɫ
	'acl_roleSessionKey'=>'ep_acl_role',
	//�Ƿ����� acl
	'acl_enable'=>true,
	//Ĭ��Ȩ��
	'acl_default'=>'all',
	
	//�������𸲸�-------
	
	//���Ŀ¼,Ĭ��������ĿĿ¼��lib����,������Ҫ���Ը���
	'lib_dir'=>dirname(__FILE__).DS.'lib',
	//��ĿĿ¼,�� globalconfig.php ��ͬһ��Ŀ¼
	'project_dir'=>dirname(__FILE__),

);

//װ�ؿ������
require_once($config['lib_dir'].DS.'main.php');
//��ʼ������
E::instance($config);
