<?php
/*
	by welling fine
	todo: be more elegance
*/
class EP_Controller{
	
	//传递到页面的参数
	protected $_views;
	
	function __execute($act,$args=array()){
		$actFullName='action'.$act;
		if(method_exists($this,$actFullName)){
			if(!$this->onBeforeExecute($act))return ;

			// TODO:action的返回值要来干什么呢？
			$viewArgs=call_user_func_array(array($this,$actFullName),$args);
			$this->onAfterExecute($act);

			$suc=E::instance()->displayView($act,$this->_views);
			if(!$suc){//view not found
				E::log('view:'.$act.' not found');
				E::instance()->displayView(E::config('view_not_found'));
			}
		}else{
			$this->onActionUndefined($act);
		}
	}
	protected function onBeforeExecute($actionName){
		return true;
	}
	protected function onAfterExecute($actionName){
		return true;
	}
	protected function onActionUndefined($actionName){
		E::instance()->displayView(E::config('action_not_found'));
		//throw new Exception('undefined action ['.$actionName.']');
		E::log('undefined action ['.$actionName.']','error');
	}
}

