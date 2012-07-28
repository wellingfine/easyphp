<?php
class EP_Controller{
	
	//传递到页面的参数
	protected $_views;
	protected $_viewObject;
	
	function __execute($act,$args=array()){
		$actFullName='action'.$act;
		if(method_exists($this,$actFullName)){
			$this->viewObject=new EP_View();
		
			if(!$this->onBeforeExecute($act))return ;
			
			// TODO:action的返回值要来干什么呢？
			$viewArgs=call_user_func_array(array($this,$actFullName),$args);
			$this->onAfterExecute($act);
			E::log('create view...','core');
			
			$this->viewObject->show($this->_views);
		}else{
			$this->onActionUndefined($act);
		}
	}
	// 给一个自定义模板的方法，方便在controller 临时有构建页面片的需求
	function showPage($name,$args=''){
		$this->_viewObject->render($name,$args);
	}
	
	protected function onBeforeExecute($actionName){
		return true;
	}
	protected function onAfterExecute($actionName){
		return true;
	}
	protected function onActionUndefined($actionName){
		$this->showPage(E::config('not_found_page'));
		//throw new Exception('undefined action ['.$actionName.']');
		E::log('undefined action ['.$actionName.']','error');
	}
}

