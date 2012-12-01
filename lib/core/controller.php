<?php
/*
	by welling fine
	todo: be more elegance
*/
abstract class EP_Controller{
	
	//传递到页面的参数
	protected $_views=array();
	
	//if json is null , then use view.
	//if json not null, echo it ! note: if you want to output json this way ,make sure your controller don't  output any strings
	protected $_json=null;
	
	function __execute($act,$args=array()){
		$actFullName='action'.$act;
		if(method_exists($this,$actFullName)){
			if(!$this->onBeforeExecute($act))return ;

			$ret=call_user_func_array(array($this,$actFullName),$args);
			$this->onAfterExecute($act,$ret);
		}else{
			$this->onActionUndefined($act);
		}
	}
	//
	protected function onBeforeExecute($actionName){
		return true;
	}
	//
	protected function onAfterExecute($actionName,$ret){
		return true;
	}
	protected function onActionUndefined($actionName){
		E::instance()->displayView(E::config('action_not_found'));
		//throw new Exception('undefined action ['.$actionName.']');
		E::log('undefined action ['.$actionName.']','error');
	}
}

