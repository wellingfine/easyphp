<?php
// role base access control
// by welling 2012-07-30
/*
	ʹ������ƥ���û���ɫ,�����������֤ͨ��
	�ŵ����ڿ��Զ���һ���ɫ(eg. admin_aaa,admin_bbb)��Ȼ��һ������㶨��
	Ҳ������
*/
class EP_Rbac{
	/*
	
	'controller'=>array(
		'rule'=>'',
		'actions'=>array(
			'abc'=>'',
		),
	),	
	����ʹ��������action û�������򣬾�ʹ��controller�ģ�controllerû�������ȫ��Ĭ�Ϲ���
	*/
	public static function identify($controllerName,$actionName){
		$dft=E::config('rbac_default');
		$acl=require_once(E::config('app_dir').DS.'acl.php');
		
		//������
		$ctrl=E::get($controllerName,array(),$acl);
		$testedAclRule=E::get('rule',$dft,$ctrl);
		//action
		$act=E::get('actions',array(),$ctrl);
		$testedAclRule=E::get($actionName,$testedAclRule,$act);
		
		//replace / to \/ and add ^$ to it 
		$testedAclRule='/^'.preg_replace('/\//','\\/',$testedAclRule).'$/';
		$role=E::instance()->getRole();
		if(preg_match($testedAclRule,$role)){
			return true;
		}
		return false;
	}
}
