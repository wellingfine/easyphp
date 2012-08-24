<?php
// role base access control
// by welling 2012-07-30
/*
	使用正则匹配用户角色,如果符合则验证通过
	优点在于可以定义一类角色(eg. admin_aaa,admin_bbb)，然后一条正则搞定。
	也简单易行
*/
class EP_Rbac{
	/*
	
	'controller'=>array(
		'rule'=>'',
		'actions'=>array(
			'abc'=>'',
		),
	),	
	优先使用子正则：action 没定义正则，就使用controller的，controller没定义就用全局默认规则
	*/
	public static function identify($controllerName,$actionName){
		$dft=E::config('rbac_default');
		$acl=require_once(E::config('app_path').'acl.php');
		
		//控制器
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
